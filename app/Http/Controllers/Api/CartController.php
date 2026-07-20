<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CurrencyRate;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    
    public function getCart(Request $request)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['success' => false,'message' => 'User not authenticated'], 401);
        }
        $cart = Cart::with(['product', 'variant'])->where('user_id', $user->id)->get();
        return response()->json(['success' => true,'data' => $cart]);
    }
    
    public function updateCart(Request $request)
    {
        $productId = $request->input('product_id');
        $variantId = $request->input('product_variant_id');
        $userId = Auth::id();

        if(!$userId) {
            return response()->json(['success' => false,'message' => 'User not authenticated'], 401);
        }
        if(!$productId) {
            return response()->json(['success' => false,'message' => 'Product ID is required'], 400);
        }   
        
        $product = Product::find($productId);
        if(!$product) {
            return response()->json(['success' => false,'message' => 'Product not found'], 404);
        }

        $variant = null;
        if ($variantId) {
            $variant = \App\Models\ProductVariant::where('product_id', $productId)->where('id', $variantId)->first();
            if (!$variant) {
                return response()->json(['success' => false,'message' => 'Variant not found'], 404);
            }
        }

        $maxStock = $variant ? $variant->stock : $product->stock;

        try{
            DB::beginTransaction();
            $cart = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->lockForUpdate()
                ->first();

            if($request->action && $request->action === 'decrease') {
                if($cart && $cart->quantity > 1) {
                    $cart->quantity--;
                    $cart->save();
                } else {
                    if ($cart) {
                        $cart->delete();
                    }
                }
            } else {
                if($cart) {
                    if($cart->quantity >= $maxStock) {
                        return response()->json(['success' => false,'message' => 'Product out of stock'], 400);
                    }
                    $cart->quantity++;
                    $cart->save();
                } else {
                    if($maxStock < 1) {
                        return response()->json(['success' => false,'message' => 'Product out of stock'], 400);
                    }
                    $cart = New Cart();
                    $cart->user_id = $userId;
                    $cart->product_id = $productId;
                    $cart->product_variant_id = $variantId;
                    $cart->quantity = 1;
                    $cart->save();
                }
            }
            DB::commit();
            return response()->json(['success' => true,'message' => 'Cart Updated Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update cart: ' . $e->getMessage());
            return response()->json(['success' => false,'message' => 'Failed to update cart'], 500);
        }
    }

    public function convertAmount(Request $request)
    {
        $amount = $request->input('amount');
        $fromCurrency = $request->input('from');
        $toCurrency = $request->input('to');
        if(!$amount || !$fromCurrency || !$toCurrency) {
            return response()->json(['success' => false,'message' => 'Amount, from and to currencies are required'], 400);
        }
        $convertedAmount = CurrencyRate::convert($amount, $fromCurrency, $toCurrency);
        return response()->json(['success' => true,'convertedAmount' => $convertedAmount]);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }
        $addressId = $request->input('address_id');
        if (!$addressId) {
            return response()->json(['success' => false, 'message' => 'Address ID is required'], 400);
        }
        $request->session()->put('selected_address_id', $addressId);
        return response()->json(['success' => true, 'message' => 'Checkout initiated']);
    }
}
