<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// use App\Models\User;
// use Illuminate\Support\Str;
// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
// use Illuminate\Http\JsonResponse;
// use App\Http\Resources\UserResource;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Validator;
// use App\Http\Requests\UserLoginRequest;
// use App\Http\Requests\UserUpdateRequest;
// use App\Http\Requests\UserRegisterRequest;
// use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->count() == 1) {
            throw new HttpResponseException(response([
                "errors" => [
                    "email" => [
                        "email already exist"
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "email or password wrong"
                    ]
                ]
            ], 401)); //401 is unauthorized
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function getUser(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function updateUser(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();
        $users = Auth::user();

        if (isset($data['name'])) {
            $users->name = $data['name'];
        }

        if (isset($data['password'])) {
            $users->password = Hash::make($data['password']);
        }

        $users->save();

        return new UserResource($users);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
