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
        // Update old menu names and prices to avoid duplication and preserve IDs
        Menu::where('nama_menu', 'Nasi bakar regular ( tidak mix)')->update([
            'nama_menu' => 'Nasi bakar reguler',
            'harga' => 10000
        ]);
        Menu::where('nama_menu', 'Nasi bakar reguler (Lauk Biasa)')->update([
            'nama_menu' => 'Nasi bakar reguler',
            'harga' => 10000
        ]);
        Menu::where('nama_menu', 'Nasi bakar reguler (Lauk Premium: Cumi/Tetelan/Paru)')->delete();
        Menu::where('nama_menu', 'Nasi bakar jumbo ( porsi ekstra)')->update([
            'nama_menu' => 'Nasi bakar jumbo',
            'harga' => 15000
        ]);
        Menu::where('nama_menu', 'Nasi bakar mix')->update([
            'harga' => 12000
        ]);

        $defaultLauk = [
            'Ayam suwir',
            'Ikan tuna',
            'Usus ayam',
            'Ati ayam',
            'Ampela ayam',
            'Telur dadar',
            'Kulit sapi/ kikil / cecek',
            'Cumi *',
            'Tetelam *',
            'Paru *',
        ];

        $defaultSambal = [
            'Sambal bawang',
            'Sambal ijo',
            'Sambal pedas manis',
            'Sambal matah',
            'Sambal nanas*',
            'Sambal bajak*',
            'Tanpa sambal',
        ];

        $defaultEkstra = [
            'Jamur krispi*',
            'Telur asin*',
            'Sate kulit*',
        ];

        $menus = [
            [
                'nama_menu' => 'Nasi bakar reguler',
                'kategori' => 'makanan',
                'harga' => 10000,
                'stok' => 50,
                'status' => 'tersedia',
                'maksimal_lauk' => 1,
                'wajib_pilih_lauk' => true,
                'wajib_pilih_sambal' => true,
            ],
            [
                'nama_menu' => 'Nasi bakar mix',
                'kategori' => 'makanan',
                'harga' => 12000,
                'stok' => 50,
                'status' => 'tersedia',
                'maksimal_lauk' => 2,
                'wajib_pilih_lauk' => true,
                'wajib_pilih_sambal' => true,
            ],
            [
                'nama_menu' => 'Nasi bakar jumbo',
                'kategori' => 'makanan',
                'harga' => 15000,
                'stok' => 50,
                'status' => 'tersedia',
                'maksimal_lauk' => 1,
                'wajib_pilih_lauk' => true,
                'wajib_pilih_sambal' => true,
            ],
        ];

        foreach ($menus as $menu) {
            $model = Menu::updateOrCreate(
                ['nama_menu' => $menu['nama_menu']],
                $menu
            );

            $model->options()->delete();

            $sortOrder = 0;
            foreach ($defaultLauk as $option) {
                $model->options()->create([
                    'nama_opsi' => $option,
                    'tipe' => 'lauk',
                    'status' => 'tersedia',
                    'sort_order' => $sortOrder++,
                ]);
            }

            foreach ($defaultSambal as $option) {
                $model->options()->create([
                    'nama_opsi' => $option,
                    'tipe' => 'sambal',
                    'status' => 'tersedia',
                    'sort_order' => $sortOrder++,
                ]);
            }

            foreach ($defaultEkstra as $option) {
                $model->options()->create([
                    'nama_opsi' => $option,
                    'tipe' => 'ekstra_lauk',
                    'status' => 'tersedia',
                    'sort_order' => $sortOrder++,
                ]);
            }
        }
    }
}
