<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductRatingController extends Controller
{
    public function index(Request $request, $productId)
    {
        $query = ProductRating::with('user')->where('product_id', $productId);
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        $averageRating = round($query->avg('rating'), 1);
        $ratings = $query->paginate($request->per_page ?? 5);
        return response()->json(['success' => true, 'data' => $ratings, 'average_rating' => $averageRating]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $existingRating = ProductRating::where('product_id', $request->product_id)->where('user_id', Auth::id())->first();

        if ($existingRating) {
            $existingRating->rating = $request->rating;
            $existingRating->review = $request->review;
            $existingRating->save();
            return response()->json(['success' => true, 'message' => 'Rating updated successfully']);
        }

        ProductRating::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json(['success' => true, 'message' => 'Rating added successfully']);
    }

    public function deleteRating($id){
        try{
            $rating = ProductRating::find($id);
            $rating->delete();
            return response()->json(['success' => true, 'message' => 'Rating deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
