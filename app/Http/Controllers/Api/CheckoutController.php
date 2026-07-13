<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
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
        return response()->json(['success' => true,'data' => ['cart_items' => $cartItems,'address' => $address]]);
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
            'address_id' => 'required|integer|exists:addresses,id'
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

            // Create order with items as JSON
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
                'payment_intent_id' => $request->payment_intent_id,
                'status' => 'pending',
                'currency' => $user->currency,
                'items' => $itemsArray
            ]);

            // Clear cart
            $user->cart()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
