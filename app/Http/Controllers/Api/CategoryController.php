<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()->withCount('products');
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('status', 'like', '%' . $request->search . '%');
                $q->orWhereDate('created_at', 'like', '%' . $request->search . '%');
            });
        }
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $data = $query->paginate($request->per_page ?? 10);
        return response()->json(['success' => true,'data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:category,id',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        if($request->id) {
            $category = Category::find($request->id);
            if(!$category) {
                return response()->json(['success' => false, 'message' => 'Category not found'], 404);
            }
        } else {
            $category = new Category();
        }
        $category->name = $request->name;
        $category->status = $request->status;
        if($category->save()){
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::select('id', 'name','status')->where('id', $id)->first();
        if(!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        } else {
            return response()->json(['success' => true,'data' => $category]);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if(!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['success' => true]);
    }

    public function getCategories()
    {
        $data = Category::where('status', 'Active')->get();
        return response()->json(['success' => true,'data' => $data]);
    }
}