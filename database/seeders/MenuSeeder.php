<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'nama_menu' => 'Nasi bakar regular ( tidak mix)',
                'kategori' => 'makanan',
                'harga' => 15000,
                'stok' => 50,
                'status' => 'tersedia',
            ],
            [
                'nama_menu' => 'Nasi bakar mix',
                'kategori' => 'makanan',
                'harga' => 20000,
                'stok' => 50,
                'status' => 'tersedia',
            ],
            [
                'nama_menu' => 'Nasi bakar jumbo ( porsi ekstra)',
                'kategori' => 'makanan',
                'harga' => 25000,
                'stok' => 50,
                'status' => 'tersedia',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['nama_menu' => $menu['nama_menu']],
                $menu
            );
        }
    }
}
