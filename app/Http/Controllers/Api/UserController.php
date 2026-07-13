<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {        
        $query = User::query();

        if($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('email', 'like', '%' . $request->search . '%');
                $q->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $data = $query->paginate($request->per_page ?? 10);
        return response()->json(['success' => true,'data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $rules =  [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
            'currency' => 'required|string|max:3',
        ];

        if(!$request->id) {
            $rules['password'] = 'required|string|min:6';
            $rules['password_confirmation'] = 'required|same:password';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed','errors' => $validator->errors()->first()]);
        }

        if($request->id) {
            $user = User::find($request->id);
            if(!$user) {
                return response()->json(['success' => false, 'message' => 'User not found']);
            }
        } else {
            $user = new User();
            $user->password = Hash::make($request->password);
        }
        $user->currency = $request->currency;
        $user->name = $request->name;   
        $user->email = $request->email;
        $user->status = $request->status;
        
        if($user->save()){
            return response()->json(['success' => true, 'message' => 'User Stored successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to Store user']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::select('id', 'name', 'email', 'status', 'currency')->where('id', $id)->first();
        if(!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        return response()->json(['success' => true,'data' => $user]);
    }

    public function show($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        return response()->json(['success' => true,'data' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }
}
