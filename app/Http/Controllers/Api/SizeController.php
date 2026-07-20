<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $sizes = Size::orderBy('name', 'asc')->paginate($request->per_page ?? 10);
        return response()->json(['success' => true, 'data' => $sizes]);
    }

    public function getSizes()
    {
        $sizes = Size::orderBy('name', 'asc')->get();
        return response()->json(['success' => true, 'data' => $sizes]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:sizes,id',
            'name' => 'required|string|max:50|unique:sizes,name,' . ($request->id ?? 'NULL'),
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $size = Size::updateOrCreate(
            ['id' => $request->id],
            ['name' => $request->name]
        );

        return response()->json(['success' => true, 'data' => $size]);
    }

    public function show($id)
    {
        $size = Size::find($id);
        if (!$size) {
            return response()->json(['success' => false, 'message' => 'Size not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $size]);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->store($request);
    }

    public function destroy($id)
    {
        $size = Size::find($id);
        if (!$size) {
            return response()->json(['success' => false, 'message' => 'Size not found'], 404);
        }
        $size->delete();
        return response()->json(['success' => true, 'message' => 'Size deleted successfully']);
    }
}
