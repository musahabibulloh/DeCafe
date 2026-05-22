<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nama_menu',
    'kategori',
    'deskripsi',
    'harga',
    'stok',
    'status',
    'gambar',
])]
class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
