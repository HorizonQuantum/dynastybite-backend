<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderStatus::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
        ]);

        $OrderStatus= OrderStatus::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$OrderStatus,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderStatus $OrderStatus)
    {
        return $OrderStatus;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderStatus $OrderStatus)
    {
        $OrderStatus->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$OrderStatus,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderStatus $OrderStatus)
    {
        $OrderStatus->delete();
        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
