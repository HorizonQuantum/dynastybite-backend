<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Payment::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'order_id' => 'required|exists|orders,id',
            'method' => 'required|string',
            'amount' => 'required|integer|min:0',
        ]);

        $Payment= Payment::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$Payment,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $Payment)
    {
        return $Payment;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $Payment)
    {
        $Payment->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$Payment,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $Payment)
    {
        $Payment->delete();
        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
