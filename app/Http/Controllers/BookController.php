<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookCreateRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\In;
use PhpParser\Node\Stmt\Return_;

class BookController extends Controller
{
    public function createBook(BookCreateRequest $request): JsonResponse
    {

        $data = $request->validated();
        $user = Auth::user();

        $book = new Book($data);
        $book->user_id = $user->id;
        $book->save();

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    public function getBook(int $id): BookResource
    {
        $user = Auth::user();
        $book = Book::where('id', $id)->where('user_id', $user->id)->first();

        if (!$book) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new BookResource($book);
    }

    public function updateBook(int $id, BookUpdateRequest $request): BookResource
    {
        $user = Auth::user();
        $book = Book::where('id', $id)->where('user_id', $user->id)->first();

        if (!$book) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $data = $request->validated();
        $book->fill($data);
        $book->save();
        return new BookResource($book);
    }

    public function deleteBook(int $id): JsonResponse
    {
        $user = Auth::user();
        $book = Book::where('id', $id)->where('user_id', $user->id)->first();

        if (!$book) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $book->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function searchBook(Request $request): BookCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $books = Book::query()->where('user_id', $user->id);

        $books = $books->where(function (Builder $builder) use ($request) {
            $judul = $request->input('judul');
            if ($judul) {
                $builder->where('judul', 'like', '%' . $judul . '%');
            }

            $penulis = $request->input('penulis');
            if ($penulis) {
                $builder->where('penulis', 'like', '%' . $penulis . '%');
            }
        });

        $books->paginate(perPage: $size, page: $page);
        return new BookCollection($books);
    }
}
