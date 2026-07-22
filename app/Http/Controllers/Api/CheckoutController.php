<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private function calculateShipping($subtotal)
    {
        $charges = config('custom.small_cart_charges', []);
        foreach ($charges as $range => $fee) {
            $limits = explode('-', $range);
            if (count($limits) === 2) {
                $min = floatval($limits[0]);
                if ($limits[1] === '*') {
                    if ($subtotal >= $min) {
                        return floatval($fee);
                    }
                } else {
                    $max = floatval($limits[1]);
                    if ($subtotal >= $min && $subtotal < $max) {
                        return floatval($fee);
                    }
                }
            }
        }
        return 0.00;
    }

    public function getCheckoutData(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }
        $cartItems = $user->cart()->with(['product', 'variant'])->get();
        $addressId = $request->session()->get('selected_address_id');
        if (!$addressId) {
            $defaultAddress = $user->addresses()->where('is_default', 1)->first();
            $addressId = $defaultAddress ? $defaultAddress->id : null;
        }
        $address = $addressId ? Address::find($addressId) : null;
        $shippingRules = config('custom.small_cart_charges', []);
        return response()->json(['success' => true,'data' => ['cart_items' => $cartItems,'address' => $address, 'shipping_rules' => $shippingRules]]);
    }

    public function createPaymentIntent(Request $request)
    {
        $user = Auth::user();
        try{   
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => $user->currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
            return response()->json(['success' => true, 'clientSecret' => $paymentIntent->client_secret]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'address_id' => 'required|integer|exists:addresses,id',
            'coupon_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Get cart items and calculate total first (for validation before transaction)
        $cartItems = $user->cart()->with(['product', 'variant'])->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            $itemPrice = $cartItem->variant ? $cartItem->variant->converted_price : $cartItem->product->converted_price;
            $totalAmount += $itemPrice * $cartItem->quantity;
        }

        // Validate coupon
        $couponId = null;
        $discountAmount = 0;
        $subtotalWithDiscount = $totalAmount;

        if ($request->coupon_code) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
            if (!$coupon) {
                return response()->json(['success' => false, 'message' => 'Coupon code not found'], 404);
            }
            if (!$coupon->canUserUse($user)) {
                return response()->json(['success' => false, 'message' => 'This coupon is not available for you'], 403);
            }
            if ($coupon->min_purchase_amount && $totalAmount < $coupon->min_purchase_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum purchase amount of {$coupon->min_purchase_amount} {$user->currency} required"
                ], 400);
            }
            $discountAmount = $coupon->calculateDiscount($totalAmount);
            $subtotalWithDiscount = $totalAmount - $discountAmount;
            $couponId = $coupon->id;
        }

        $shippingAmount = $this->calculateShipping($totalAmount);
        $finalAmount = $subtotalWithDiscount + $shippingAmount;

        // Retrieve and Validate Stripe Payment Intent
        try {
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment was not successful. Stripe status: ' . $paymentIntent->status
                ], 400);
            }

            if (strtoupper($paymentIntent->currency) !== strtoupper($user->currency)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment currency mismatch.'
                ], 400);
            }

            $expectedCents = (int) round($finalAmount * 100);
            if (abs($paymentIntent->amount - $expectedCents) > 1) { // 1 cent buffer for rounding
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount mismatch. Expected: ' . $finalAmount . ' ' . $user->currency . ', Paid: ' . ($paymentIntent->amount / 100)
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe transaction validation failed: ' . $e->getMessage()
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Re-fetch cart items locked for update to prevent race conditions / overselling
            $cartItems = $user->cart()->with(['product', 'variant'])->lockForUpdate()->get();
            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
            }

            // Verify stock again before decrementing
            foreach ($cartItems as $cartItem) {
                $maxStock = $cartItem->variant ? $cartItem->variant->stock : $cartItem->product->stock;
                if ($maxStock < $cartItem->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Product ' . $cartItem->product->name . ($cartItem->variant ? ' (' . $cartItem->variant->name . ')' : '') . ' is out of stock'
                    ], 400);
                }
            }

            $itemsArray = [];
            foreach ($cartItems as $cartItem) {
                $itemPrice = $cartItem->variant ? $cartItem->variant->converted_price : $cartItem->product->converted_price;
                $itemTotal = $itemPrice * $cartItem->quantity;
                $itemsArray[] = [
                    'product_id' => $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'product_name' => $cartItem->product->name . ($cartItem->variant ? ' (' . $cartItem->variant->name . ')' : ''),
                    'product_image' => $cartItem->product->image,
                    'quantity' => $cartItem->quantity,
                    'price' => $itemPrice,
                    'total' => $itemTotal
                ];
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'total_amount' => round($totalAmount, 2),
                'shipping' => round($shippingAmount, 2),
                'discount_amount' => round($discountAmount, 2),
                'final_amount' => round($finalAmount, 2),
                'payment_status' => 'paid',
                'payment_intent_id' => $request->payment_intent_id,
                'status' => 'pending',
                'currency' => $user->currency,
                'items' => $itemsArray,
                'coupon_id' => $couponId,
            ]);

            // Save items inside relation table and update stock levels
            foreach ($cartItems as $cartItem) {
                $itemPrice = $cartItem->variant ? $cartItem->variant->converted_price : $cartItem->product->converted_price;
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $itemPrice
                ]);

                if ($cartItem->product_variant_id) {
                    $cartItem->variant->decrement('stock', $cartItem->quantity);
                } else {
                    $cartItem->product->decrement('stock', $cartItem->quantity);
                }
            }

            // Increment coupon usage if applied
            if ($couponId) {
                $coupon->increment('times_used');
            }

            // Clear cart
            $user->cart()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'data' => [
                    'order_id' => $order->id,
                    'total_amount' => round($totalAmount, 2),
                    'discount_amount' => round($discountAmount, 2),
                    'final_amount' => round($finalAmount, 2),
                    'currency' => $user->currency,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to place order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }
}
