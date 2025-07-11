<?php

namespace App\Http\Controllers;

use App\Models\TypeOrder;
use Illuminate\Http\Request;

class TypeOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TypeOrder::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
        ]);

        $TypeOrder= TypeOrder::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$TypeOrder,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeOrder $TypeOrder)
    {
        return $TypeOrder;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeOrder $TypeOrder)
    {
        $TypeOrder->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$TypeOrder,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeOrder $TypeOrder)
    {
        $TypeOrder->delete();
        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
