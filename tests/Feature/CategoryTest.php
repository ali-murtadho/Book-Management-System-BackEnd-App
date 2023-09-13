<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Database\Seeders\BookSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function testCreateCategorySuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->post(
            '/api/books/' . $book->id . '/categories',
            [
                'categoryName' => 'test'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'categoryName' => 'test'
                ]
            ]);
    }

    public function testCreateCategoryFailed()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->post(
            '/api/books/' . $book->id . '/categories',
            [
                'categoryName' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ullam quod, unde minus soluta fugiat obcaecati non quaerat ut sint, tenetur eligendi sequi corrupti veritatis, quos nisi aliquid aperiam eaque accusantium earum sapiente expedita? Ipsa natus corporis, recusandae itaque nihil dicta ipsam sequi placeat? Facere.'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'categoryName' => [

                        'The category name field must not be greater than 50 characters.'
                    ]
                ]
            ]);
    }

    public function testCreateCategoryNotFound()
    {
        $this->seed([UserSeeder::class, BookSeeder::class]);
        $book = Book::query()->limit(1)->first();

        $this->post(
            '/api/books/' . ($book->id + 1) . '/categories',
            [
                'categoryName' => 'test'
            ],
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $category = Category::query()->limit(1)->first();
        $this->get('/api/books/' . $category->book_id . '/categories/' . $category->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'categoryName' => 'testCat'
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $category = Category::query()->limit(1)->first();
        $this->put(
            '/api/books/' . $category->book_id . '/categories/' . $category->id,
            [
                'categoryName' => 'updateTestCat'

            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'categoryName' => 'updateTestCat'
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $category = Category::query()->limit(1)->first();
        $this->put(
            '/api/books/' . $category->book_id . '/categories/' . $category->id,
            [
                'categoryName' => ''

            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'categoryName' => [
                        'The category name field is required.'
                    ]
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $category = Category::query()->limit(1)->first();
        $this->put(
            '/api/books/' . ($category->book_id + 1) . '/categories/' . $category->id,
            [
                'categoryName' => 'testCat'

            ],
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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $category = Category::query()->limit(1)->first();
        $this->delete(
            '/api/books/' . $category->book_id . '/categories/' . $category->id,
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $category = Category::query()->limit(1)->first();
        $this->delete(
            '/api/books/' . ($category->book_id + 1) . '/categories/' . $category->id,
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' =>
                    [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $book = Book::query()->limit(1)->first();
        $this->get(
            '/api/books/' . $book->id . '/categories/',

            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'categoryName' => 'testCat'
                    ]
                ]
            ]);
    }

    public function testListNotFound()
    {
        $this->seed([UserSeeder::class, BookSeeder::class, CategorySeeder::class,]);
        $book = Book::query()->limit(1)->first();
        $this->get(
            '/api/books/' . ($book->id + 1) . '/categories/',

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
}
