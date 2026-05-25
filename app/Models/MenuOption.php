<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'menu_id',
    'nama_opsi',
    'tipe',
    'status',
    'sort_order',
])]
class MenuOption extends Model
{
    use HasFactory;

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
