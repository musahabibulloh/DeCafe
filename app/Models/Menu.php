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
    'maksimal_lauk',
    'wajib_pilih_lauk',
    'wajib_pilih_sambal',
])]
class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    protected $casts = [
        'maksimal_lauk' => 'integer',
        'wajib_pilih_lauk' => 'boolean',
        'wajib_pilih_sambal' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function options()
    {
        return $this->hasMany(MenuOption::class)->orderBy('sort_order')->orderBy('nama_opsi');
    }

    public function getGambarUrlAttribute()
    {
        if (empty($this->gambar)) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($this->gambar, ['http://', 'https://'])) {
            return $this->gambar;
        }

        return asset('storage/' . $this->gambar);
    }
}
