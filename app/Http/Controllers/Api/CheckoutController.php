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
        $cartItems = $user->cart()->with('product')->get();
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
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'address_id' => 'required|integer|exists:addresses,id',
            'coupon_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            DB::beginTransaction();

            // Get cart items
            $cartItems = $user->cart()->with('product')->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty']);
            }

            // Calculate total and prepare items array
            $totalAmount = 0;
            $itemsArray = [];

            foreach ($cartItems as $cartItem) {
                $itemTotal = $cartItem->product->converted_price * $cartItem->quantity;
                $totalAmount += $itemTotal;

                $itemsArray[] = [
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_image' => $cartItem->product->image,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->converted_price,
                    'total' => $itemTotal
                ];

                // Update product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Apply coupon if provided
            $couponId = null;
            $discountAmount = 0;
            $subtotalWithDiscount = $totalAmount;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
                
                if (!$coupon) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Coupon code not found'], 404);
                }

                if (!$coupon->canUserUse($user)) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'This coupon is not available for you'], 403);
                }

                // Check minimum purchase amount (in user's currency)
                if ($coupon->min_purchase_amount && $totalAmount < $coupon->min_purchase_amount) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Minimum purchase amount of {$coupon->min_purchase_amount} {$user->currency} required"
                    ], 400);
                }

                // Calculate discount (works with any currency)
                $discountAmount = $coupon->calculateDiscount($totalAmount);
                $subtotalWithDiscount = $totalAmount - $discountAmount;
                $couponId = $coupon->id;
            }

            $shippingAmount = $this->calculateShipping($totalAmount);
            $finalAmount = $subtotalWithDiscount + $shippingAmount;

            // Create order with items as JSON
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
                'message' => $e->getMessage()
            ]);
        }
    }
}
