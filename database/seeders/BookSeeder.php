<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@gmail.com')->first();
        Book::create([
            'judul' => 'tes',
            'tahun_terbit' => '2002',
            'penulis' => 'test',
            'user_id' => $user->id
        ]);
    }
}
