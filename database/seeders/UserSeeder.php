<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@decafe.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@decafe.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir',
                'email' => 'kasir@decafe.test',
                'role' => 'kasir',
            ],
            [
                'name' => 'Pelayan',
                'email' => 'pelayan@decafe.test',
                'role' => 'pelayan',
            ],
            [
                'name' => 'Dapur',
                'email' => 'dapur@decafe.test',
                'role' => 'dapur',
            ],
            [
                'name' => 'Pelanggan',
                'email' => 'pelanggan@decafe.test',
                'role' => 'customer',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
