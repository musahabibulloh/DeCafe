<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nama_lauk',
    'tipe',
    'harga',
    'is_premium',
    'gambar',
])]
class Lauk extends Model
{
    use HasFactory;

    protected $casts = [
        'is_premium' => 'boolean',
    ];

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
