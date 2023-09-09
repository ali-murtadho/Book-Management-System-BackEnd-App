<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'name' => 'Ali Murtadho',
            'email' => 'alimur16@gmail.com',
            'password' => 'alimur16'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'name' => 'Ali Murtadho',
                    'email' => 'alimur16@gmail.com'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'name' => '',
            'email' => '',
            'password' => ''
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'name' => [
                        "The name field is required."
                    ],
                    'email' => [
                        "The email field is required."
                    ],
                    'password' => [
                        "The password field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterEmail()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'name' => 'Ali Murtadho',
            'email' => 'alimur16@gmail.com',
            'password' => 'alimur16'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'email' => [
                        "email already exist"
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'email' => 'test@gmail.com',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@gmail.com',
                    'name' => 'test'
                ]
            ]);

        $user = User::where('email', 'test@gmail.com')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedEmailNotFound()
    {

        $this->post('/api/users/login', [
            'email' => 'wrong@gmail.com',
            'password' => 'test'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "email or password wrong"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'email' => 'test@gmail.com',
            'password' => 'password salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "email or password wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@gmail.com',
                    'name' => 'test'
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('email', 'test@gmail.com')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'ali'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@gmail.com',
                    'name' => 'ali'
                ]
            ]);
        $newUser = User::where('email', 'test@gmail.com')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('email', 'test@gmail.com')->first();

        $this->patch(
            '/api/users/current',
            [
                'password' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@gmail.com',
                    'name' => 'test'
                ]
            ]);
        $newUser = User::where('email', 'test@gmail.com')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdatefailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            '/api/users/current',
            [
                'name' => 'Lorem ipsum dolor sit amet, 
                consectetur adipisicing elit. Tempore atque maiores 
                minima voluptas unde distinctio voluptate repudiandae, 
                natus iste incidunt iure nisi doloremque deserunt nesciunt 
                enim aperiam minus esse soluta facere odit rem libero? Corporis 
                ratione quos quam eos? Tenetur in corrupti et recusandae accusamus 
                placeat ea dolorem id numquam!'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::where('email', 'test@gmail.com')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]

            ]);
    }
}
