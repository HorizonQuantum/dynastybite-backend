<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderItem::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'menu_id' => 'required|exists|menus,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
            'total' => 'required|integer|min:0',
            'pcs' => 'required|integer|min:0'
        ]);

        $OrderItem= OrderItem::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$OrderItem,
        ], 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(OrderItem $OrderItem)
    {
        return $OrderItem;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderItem $OrderItem)
    {
        $OrderItem->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$OrderItem,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderItem $OrderItem)
    {
        $OrderItem->delete();
        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
