<?php

namespace App\Http\Controllers;

use App\Models\CategoriesMenu;
use Illuminate\Http\Request;

class CategoriesMenusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CategoriesMenu::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
        ]);

        $category = CategoriesMenu::create($validate);

        return response()->json([
            'messege'=>'Data berhasil disimpan',
            'data'=>$category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = CategoriesMenu::findOrFail($id);
        return response()->json($category);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = CategoriesMenu::findOrFail($id);
        $category->update($request->all());
        return response()->json([
            'messege'=>'Data berhasil diperbaharui',
            'data'=>$category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = CategoriesMenu::findOrFail($id);

        foreach ($category->menus as $menu) {
            if ($menu->image && Storage::disk('public')->exists("images/{$menu->image}")) {
                Storage::disk('public')->delete("images/{$menu->image}");
            }
            $menu->delete();
        }
        $category->delete();

        return response()->json([
            'messege'=>'Data berhasil dihapus',
        ]);
    }
}
