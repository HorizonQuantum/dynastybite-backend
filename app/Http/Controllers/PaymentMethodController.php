<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PaymentMethod::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
        ]);

        $PaymentMethod= PaymentMethod::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$PaymentMethod,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $PaymentMethod)
    {
        return $PaymentMethod;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $PaymentMethod)
    {
        $PaymentMethod->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$PaymentMethod,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $PaymentMethod)
    {
        $PaymentMethod->delete();
        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
