<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Get all coupons (Admin only)
     */
    public function index(Request $request)
    {
        $query = Coupon::query();
        $query = $this->filterQuery($query, $request);
        $data = $query->paginate($request->per_page ?? 10);
        return response()->json(['success' => true,'data' => $data]);
    }

    public function filterQuery($query, $request){
        if ($request->has('discount_type') && $request->discount_type) {
            $query->where('discount_type', $request->discount_type);
        }
        if($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%');
                $q->orWhere('description', 'like', '%' . $request->search . '%');
                $q->orWhere('discount_type', 'like', '%' . $request->search . '%');
                $q->orWhere('discount_value', 'like', '%' . $request->search . '%');
                $q->orWhere('min_purchase_amount', 'like', '%' . $request->search . '%');
                $q->orWhere('max_uses', 'like', '%' . $request->search . '%');
                $q->orWhere('times_used', 'like', '%' . $request->search . '%');
                $q->orWhere('max_uses_per_user', 'like', '%' . $request->search . '%');
                $q->orWhere('is_active', 'like', '%' . $request->search . '%');
                $q->orWhereDate('valid_from', 'like', '%' . $request->search . '%');
                $q->orWhereDate('valid_until', 'like', '%' . $request->search . '%');
                $q->orWhereDate('created_at', 'like', '%' . $request->search . '%');
            });
        }
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        return $query;
    }

    /**
     * Get single coupon (Admin only)
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 403);
        }

        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $coupon]);
    }

    /**
     * Create new coupon (Admin only)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 403);
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ]);

        try {
            $coupon = Coupon::create($validated);
            return response()->json(['success' => true, 'message' => 'Coupon created successfully', 'data' => $coupon], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create coupon'], 500);
        }
    }

    /**
     * Update coupon (Admin only)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 403);
        }

        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'string|unique:coupons,code,' . $id,
            'description' => 'nullable|string',
            'discount_type' => 'in:percentage,fixed',
            'discount_value' => 'numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ]);

        try {
            $coupon->update($validated);
            return response()->json(['success' => true, 'message' => 'Coupon updated successfully', 'data' => $coupon]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update coupon'], 500);
        }
    }

    /**
     * Delete coupon (Admin only)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 403);
        }

        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
        }

        try {
            $coupon->delete();
            return response()->json(['success' => true, 'message' => 'Coupon deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete coupon'], 500);
        }
    }

    /**
     * Validate and get coupon details (User can use)
     */
    public function validate(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $couponCode = $request->input('coupon_code');
        $cartTotal = $request->input('cart_total'); // Total amount in user's currency

        if (!$couponCode || $cartTotal === null) {
            return response()->json(['success' => false, 'message' => 'Coupon code and cart total are required'], 400);
        }

        $coupon = Coupon::where('code', strtoupper($couponCode))->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon code not found'], 404);
        }

        if (!$coupon->canUserUse($user)) {
            return response()->json(['success' => false, 'message' => 'This coupon is not available for you'], 403);
        }

        // Check minimum purchase amount (in user's currency)
        if ($coupon->min_purchase_amount && $cartTotal < $coupon->min_purchase_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum purchase amount of {$coupon->min_purchase_amount} {$user->currency} required"
            ], 400);
        }

        // Calculate discount (works with any currency)
        $discountAmount = $coupon->calculateDiscount($cartTotal);
        $finalAmount = $cartTotal - $discountAmount;

        return response()->json([
            'success' => true,
            'message' => 'Coupon is valid',
            'data' => [
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
                'description' => $coupon->description,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'discount_amount' => round($discountAmount, 2),
                'cart_total' => round($cartTotal, 2),
                'final_amount' => round($finalAmount, 2),
                'currency' => $user->currency,
            ]
        ]);
    }
}
