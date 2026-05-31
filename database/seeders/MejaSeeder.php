<?php

namespace Database\Seeders;

use App\Models\Meja;
use Illuminate\Database\Seeder;

class MejaSeeder extends Seeder
{
    /**
     * Seed tables (meja) 1-10.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Meja::updateOrCreate(
                ['nomor_meja' => (string) $i],
                []
            );
        }
    }
}
