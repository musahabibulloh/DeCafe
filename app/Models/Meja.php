<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'nomor_meja',
    'token',
    'status',
])]
class Meja extends Model
{
    protected $table = 'meja';

    /**
     * Boot the model and auto-generate token on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meja) {
            if (empty($meja->token)) {
                $meja->token = Str::random(32);
            }
        });
    }

    /**
     * Get the QR scan URL for this table.
     */
    public function getScanUrlAttribute(): string
    {
        return route('scan.meja', $this->token);
    }
}
