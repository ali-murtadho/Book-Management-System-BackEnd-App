<?php

namespace Tests\Feature;

use App\Models\Book;
use Database\Seeders\BookSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class BookTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/books',
            [
                'judul' => 'tes',
                'tahun_terbit' => '2002',
                'penulis' => 'test'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'judul' => 'tes',
                    'tahun_terbit' => '2002',
                    'penulis' => 'test'
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/books',
            [
                'judul' => '',
                'tahun_terbit' => 'jjnjn',
                'penulis' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam rem ullam aliquam aliquid assumenda laboriosam suscipit delectus porro, officia, quos nesciunt natus deserunt voluptatem repudiandae aperiam, recusandae eaque voluptatibus? Praesentium neque saepe officia officiis illo consectetur, aliquid animi, iure voluptatum earum exercitationem quis voluptates accusantium cum. Blanditiis magnam quis iure voluptatum vitae voluptate nisi numquam accusantium, neque quo quasi, cupiditate, aut amet labore nostrum quas itaque repellat quae? Nam, nemo!
                '
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "judul" => [
                        "The judul field is required."
                    ],
                    "penulis" => [
                        "The penulis field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testCreateUnautorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/books',
            [
                'judul' => '',
                'tahun_terbit' => 'jjnjn',
                'penulis' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam rem ullam aliquam aliquid assumenda laboriosam suscipit delectus porro, officia, quos nesciunt natus deserunt voluptatem repudiandae aperiam, recusandae eaque voluptatibus? Praesentium neque saepe officia officiis illo consectetur, aliquid animi, iure voluptatum earum exercitationem quis voluptates accusantium cum. Blanditiis magnam quis iure voluptatum vitae voluptate nisi numquam accusantium, neque quo quasi, cupiditate, aut amet labore nostrum quas itaque repellat quae? Nam, nemo!
                '
            ],
            [
                'Authorization' => 'wrong'
            ]
        )->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetBookSuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->get(
            '/api/books/' . $book->id,
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'judul' => 'tes',
                    'tahun_terbit' => '2002',
                    'penulis' => 'test',
                ]
            ]);
    }

    public function testGetBookNotFound()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->get('/api/books/' . ($book->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->get('/api/books/' . $book->id, [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->put(
            '/api/books/' . $book->id,
            [

                'judul' => 'tes2',
                'tahun_terbit' => '2003',
                'penulis' => 'test2',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'judul' => 'tes2',
                    'tahun_terbit' => '2003',
                    'penulis' => 'test2',
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->put(
            '/api/books/' . $book->id,
            [

                'judul' => '',
                'tahun_terbit' => '2003',
                'penulis' => 'test2',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'judul' => [
                        "The judul field is required."
                    ]
                ]
            ]);
    }
    public function testDeleteBookSuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->delete(
            '/api/books/' . $book->id,
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }
    public function testDeleteBookNotFound()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->delete(
            '/api/books/' . ($book->id + 1),
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    // public function testSearchByJudul()
    // {
    //     $this->seed([UserSeeder::class, SearchSeeder::class]);
    //     $response = $this->get('/api/books?judul=testjudul', [
    //         'Authorization' => 'test'
    //     ])
    //         ->assertStatus(200)
    //         ->json();

    //     Log::info(json_encode($response));
    //     self::assertEquals(10, count($response['data']));
    // }
}
