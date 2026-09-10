<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@gmail.com',
                'password' => Hash::make('abcde'),
                'role'     => 'superadmin',
                'phone'    => '+62 812 0000 1111',
                'avatar'   => null,
            ]
        );

        // 2. User Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('abcde'),
                'role'     => 'admin',
                'phone'    => '+62 812 2222 3333',
                'avatar'   => null,
            ]
        );
    }
}
