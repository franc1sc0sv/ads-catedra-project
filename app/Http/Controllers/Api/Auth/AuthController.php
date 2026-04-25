<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json($result, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json($result);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->bearerToken());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->user(), $request->bearerToken());

        return response()->json($result);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->authService->updateProfile($request->user(), $request->validated());

        return response()->json(new UserResource($user));
    }
}
