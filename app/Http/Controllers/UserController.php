<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'role' => 'required|string',
            'username' => 'required|string',
            'number_phone' => 'required|string',
            'password' => 'required|string',
            'address' => 'required|string',
        ]);

        $user= User::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $hasActiveOrders =\App\Models\Order::where('user_id', $user->id)
            ->where('status_id', '!=', 5)
            ->exists();

        if ($hasActiveOrders) {
            return response()->json(['message' => 'Tidak bisa menghapus karena user memiliki order aktif'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }

    // di UserController.php
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Password lama salah'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah']);
    }


}
