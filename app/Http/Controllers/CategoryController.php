<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\In;

class CategoryController extends Controller
{
    private function getCategoryData(Book $book, int $idCategory): Category
    {
        $category = Category::where('book_id', $book->id)->where('id', $idCategory)->first();
        if (!$category) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return $category;
    }

    private function getBookData(User $user, int $idBook): Book
    {
        $book = Book::where('user_id', $user->id)->where('id', $idBook)->first();
        if (!$book) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return $book;
    }

    public function createCategory(int $idBook, CategoryCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $book = $this->getBookData($user, $idBook);
        $data = $request->validated();
        $category = new Category($data);
        $category->book_id = $book->id;
        $category->save();

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function getCategory(int $idBook, int $idCategory): CategoryResource
    {
        $user = Auth::user();

        $book = $this->getBookData($user, $idBook);

        $category = $this->getCategoryData($book, $idCategory);

        return new CategoryResource($category);
    }

    public function updateCategory(int $idBook, int $idCategory, CategoryUpdateRequest $request): CategoryResource
    {
        $user = Auth::user();
        $book = $this->getBookData($user, $idBook);
        $category = $this->getCategoryData($book, $idCategory);

        $data = $request->validated();
        $category->fill($data);
        $category->save();

        return new CategoryResource($category);
    }

    public function deleteCategory(int $idBook, int $idCategory): JsonResponse
    {
        $user = Auth::user();
        $book = $this->getBookData($user, $idBook);
        $category = $this->getCategoryData($book, $idCategory);
        $category->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function listCategory(int $idBook): JsonResponse
    {
        $user = Auth::user();
        $book = $this->getBookData($user, $idBook);

        $category = Category::where('book_id', $book->id)->get();
        return (CategoryResource::collection($category))->response()->setStatusCode(200);
    }
}
