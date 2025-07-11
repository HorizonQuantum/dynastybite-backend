<?php

namespace App\Http\Controllers;

use App\Models\OrderSchedule;
use Illuminate\Http\Request;

class OrderScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderSchedule::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'order_id' => 'required|exists|orders,id',
            'date_order' => 'required|date',
            'date_delivery' => 'required|date',
        ]);

        $OrderSchedule= OrderSchedule::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$OrderSchedule,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderSchedule $OrderSchedule)
    {
        return $OrderSchedule;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderSchedule $OrderSchedule)
    {
        $OrderSchedule->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$OrderSchedule,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderSchedule $OrderSchedule)
    {
        $order_schedule->delete();
        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
