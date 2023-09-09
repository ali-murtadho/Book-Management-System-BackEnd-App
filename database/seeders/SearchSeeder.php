<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@gmail.com')->first();
        for ($i = 0; $i < 20; $i++) {
            Book::create([
                'judul' => 'testjudul' . $i,
                'penulis' => 'testpenulis' . $i,
                'user_id' => $user->id
            ]);
        }
    }
}
