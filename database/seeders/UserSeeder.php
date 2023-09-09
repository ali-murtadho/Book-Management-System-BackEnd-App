<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('test'),
            'token' => 'test'
        ]);

        User::create([
            'name' => 'test2',
            'email' => 'test2@gmail.com',
            'password' => Hash::make('test2'),
            'token' => 'test2'
        ]);
    }
}
