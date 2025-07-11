<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'price', 'description', 'image', 'category_id', 'pcs'];

    public function category()
    {
        return $this->belongsTo(CategoriesMenus::class, 'category_id');
    }

}

