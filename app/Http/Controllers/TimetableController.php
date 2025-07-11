<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Timetable::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'periode' => 'required|string',
            'day_order' => 'required|string',
            'day_delivery' => 'required|string',
        ]);

        $Timetable= Timetable::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$Timetable,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Timetable $Timetable)
    {
        return $Timetable;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Timetable $Timetable)
    {
        $Timetable->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$Timetable,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timetable $Timetable)
    {
        $Timetable->delete();
        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
