<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:6',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id,
        ]);
        if($validator->fails()) {
            return response()->json(['success' => false,'message' => $validator->errors()]);
        }
        $user = Auth::user();
        if(!$user) {
            return response()->json(['success' => false,'message' => 'User not authenticated']);
        }
        $user->update($request->all());
        return response()->json(['success' => true,'message' => 'Profile updated successfully']);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:new_password',
        ]);
        if($validator->fails()) {
            return response()->json(['success' => false,'message' => $validator->errors()->first()]);
        }
        $user = Auth::user();
        if(!$user) {
            return response()->json(['success' => false,'message' => 'User not authenticated']);
        }
        if(!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false,'message' => 'Current password is incorrect']);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json(['success' => true,'message' => 'Password changed successfully']);
    }
    
    public function getAddresses()
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['success' => false,'message' => 'User not authenticated']);
        }
        $addresses = $user->addresses;
        return response()->json(['success' => true,'data' => $addresses]);
    }

    public function updateAddress(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);
        if($validator->fails()) {
            return response()->json(['success' => false,'message' => $validator->errors()->first()]);
        }
        $user = Auth::user();
        if(!$user) {
            return response()->json(['success' => false,'message' => 'User not authenticated']);
        }

        try{
            DB::beginTransaction();
            if($request->is_default) {
                Address::where('user_id', $user->id)->update(['is_default' => 0]);
            }
            
            Address::updateOrCreate(
                ['id' => $request->id],
                [
                    'full_name' => $request->full_name,
                    'phone_number' => $request->phone,
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                    'postal_code' => $request->postal_code,
                    'user_id' => $user->id,
                    'is_default' => $request->is_default ? 1 : 0,
                ]
            );
            DB::commit();
        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false,'message' => $e->getMessage()]);
        }
        return response()->json(['success' => true,'message' => 'Address updated successfully']);
    }
}
