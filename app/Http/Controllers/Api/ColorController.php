<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $colors = Color::orderBy('name', 'asc')->paginate($request->per_page ?? 10);
        return response()->json(['success' => true, 'data' => $colors]);
    }

    public function getColors()
    {
        $colors = Color::orderBy('name', 'asc')->get();
        return response()->json(['success' => true, 'data' => $colors]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:colors,id',
            'name' => 'required|string|max:50|unique:colors,name,' . ($request->id ?? 'NULL'),
            'code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $color = Color::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'code' => $request->code
            ]
        );

        return response()->json(['success' => true, 'data' => $color]);
    }

    public function show($id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['success' => false, 'message' => 'Color not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $color]);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->store($request);
    }

    public function destroy($id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json(['success' => false, 'message' => 'Color not found'], 404);
        }
        $color->delete();
        return response()->json(['success' => true, 'message' => 'Color deleted successfully']);
    }
}
