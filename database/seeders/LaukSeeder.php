<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LaukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lauks = [
            // Lauk Utama
            [
                'nama_lauk' => 'Ayam suwir',
                'tipe' => 'utama',
                'harga' => 12000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Ikan tuna',
                'tipe' => 'utama',
                'harga' => 12000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Usus ayam',
                'tipe' => 'utama',
                'harga' => 10000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Ati ayam',
                'tipe' => 'utama',
                'harga' => 10000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Ampela ayam',
                'tipe' => 'utama',
                'harga' => 10000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Telur dadar',
                'tipe' => 'utama',
                'harga' => 10000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Kulit sapi/ kikil / cecek',
                'tipe' => 'utama',
                'harga' => 12000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Cumi *',
                'tipe' => 'utama',
                'harga' => 12000,
                'is_premium' => true,
            ],
            [
                'nama_lauk' => 'Tetelam *',
                'tipe' => 'utama',
                'harga' => 12000,
                'is_premium' => true,
            ],
            [
                'nama_lauk' => 'Paru *',
                'tipe' => 'utama',
                'harga' => 12000,
                'is_premium' => true,
            ],
            
            // Lauk Tambahan (Ekstra)
            [
                'nama_lauk' => 'Telur asin',
                'tipe' => 'tambahan',
                'harga' => 3000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Jamur krispi',
                'tipe' => 'tambahan',
                'harga' => 3000,
                'is_premium' => false,
            ],
            [
                'nama_lauk' => 'Sate kulit',
                'tipe' => 'tambahan',
                'harga' => 3000,
                'is_premium' => false,
            ],
        ];

        foreach ($lauks as $lauk) {
            \App\Models\Lauk::updateOrCreate(
                ['nama_lauk' => $lauk['nama_lauk']],
                $lauk
            );
        }
    }
}
