<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return new JsonResponse($user, Response::HTTP_CREATED);
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return new JsonResponse(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return new JsonResponse(['message' => 'Successfully logged out'], Response::HTTP_OK);
    }
}
