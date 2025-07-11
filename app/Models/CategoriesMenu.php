<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesMenu extends Model
{
    protected $table = 'categories_menus';
    protected $fillable = ['name'];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id');
    }
    // app/Models/CategoryMenu.php

    protected static function booted()
    {
        static::deleting(function ($category) {
            foreach ($category->menus as $menu) {
                // Hapus gambar dari storage juga kalau ada
                if ($menu->image && Storage::disk('public')->exists("images/{$menu->image}")) {
                    Storage::disk('public')->delete("images/{$menu->image}");
                }
                $menu->delete();
            }
        });
    }


}
