<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function updateWishlist(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:add,remove',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false,'message' => $validator->errors()->first()]);
        }
        if($request->action === 'add') {
            Wishlist::updateOrCreate(['user_id' => $user->id,'product_id' => $request->product_id],['created_at' => now()]);
        } else {
            Wishlist::where('user_id', $user->id)->where('product_id', $request->product_id)->delete();
        }
        
        return response()->json(['success' => true,'message' => 'Wishlist updated successfully','data' => $user->wishlist]);
    }

    public function getWishlist(Request $request)
    {
        $user = Auth::user();
        // $wishlist = $user->wishlist()->with('product')->get();
        $query = $user->wishlist()->with('product');
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        $wishlist = $query->paginate($request->per_page ?? 8);
        return response()->json(['success' => true,'message' => 'Wishlist retrieved successfully','data' => $wishlist]);
    }

    public function fetchWishlist()
    {
        $user = Auth::user();
        $wishlist = $user->wishlist()->with('product')->get();
        return response()->json(['success' => true,'message' => 'Wishlist retrieved successfully','data' => $wishlist]);
    }
}
