<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Menu::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pcs' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories_menus,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            
            // Simpan ke storage public
            $image->storeAs('public/images', $imageName);
            
            // Path untuk diakses dari web
            $dbImagePath = $imageName;
        } else {
            return response()->json(['message' => 'File gambar diperlukan'], 422);
        }

        $menu = Menu::create([
            'name' => $request->name,
            'pcs' => $request->pcs,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'image' => $dbImagePath,
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => $menu,
        ], 201);
    }

    /**
     * Display the specified resource.
     */ 
    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        return response()->json($menu);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'pcs' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories_menus,id',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        $request->validate($rules);

        // Update data utama
        $menu->update([
            'name' => $request->name,
            'pcs' => $request->pcs,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);

        // Handle file upload jika ada gambar baru
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            
            // Hapus gambar lama jika ada
            if ($menu->image) {
                $oldImagePath = 'public/images/' . $menu->image;
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }

            // Simpan gambar baru
            $image->storeAs('public/images', $imageName);
            
            // Update path gambar di database
            $menu->update([
                'image' => $imageName
            ]);
        }

        return response()->json([
            'message' => 'Menu berhasil diperbarui',
            'data' => $menu->fresh() // Mengambil data terbaru dari database
        ]);
    }

    public function destroy($id)
    {   
        $menu = Menu::findOrFail($id);

        try {
            // Hapus gambar terkait jika ada
            if ($menu->image) {
                $imagePath = 'public/images/' . $menu->image;
                
                // Periksa apakah file benar-benar ada sebelum menghapus
                if (Storage::exists($imagePath)) {
                    Storage::delete($imagePath);
                } else {
                    \Log::warning('Gambar tidak ditemukan di storage: ' . $imagePath);
                }
            }

            // Hapus data dari database
            $menu->delete();

            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            \Log::error('Gagal menghapus menu: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }   
}