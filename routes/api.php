<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function () {
    Route::get('/users/current', [UserController::class, 'getUser']);
    Route::patch('/users/current', [UserController::class, 'updateUser']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    Route::post('/books', [BookController::class, 'createBook']);
    Route::get('/books', [BookController::class, 'searchBook']);
    Route::get('/books/{id}', [BookController::class, 'getBook'])->where('id', '[0-9]+');
    Route::put('/books/{id}', [BookController::class, 'updateBook'])->where('id', '[0-9]+');
    Route::delete('/books/{id}', [BookController::class, 'deleteBook'])->where('id', '[0-9]+');

    Route::post('/books/{idBook}/categories', [CategoryController::class, 'createCategory'])->where('idBook', '[0-9]+');
    Route::get('/books/{idBook}/categories', [CategoryController::class, 'listCategory'])->where('idBook', '[0-9]+');
    Route::get('/books/{idBook}/categories/{idCategory}', [CategoryController::class, 'getCategory'])->where('idBook', '[0-9]+')->where('idCategory', '[0-9]+');
    Route::put('/books/{idBook}/categories/{idCategory}', [CategoryController::class, 'updateCategory'])->where('idBook', '[0-9]+')->where('idCategory', '[0-9]+');
    Route::delete('/books/{idBook}/categories/{idCategory}', [CategoryController::class, 'deleteCategory'])->where('idBook', '[0-9]+')->where('idCategory', '[0-9]+');
});
