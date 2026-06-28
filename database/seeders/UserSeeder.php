<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Gara Petshop',
            'email' => 'admin@garapetshop.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir Gara Petshop',
            'email' => 'kasir@garapetshop.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}