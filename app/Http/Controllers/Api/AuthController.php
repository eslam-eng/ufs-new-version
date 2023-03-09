<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreFcmTokenRequest;
use App\Http\Resources\AuthUserResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(public AuthService $authService)
    {
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->authService->loginWithEmailOrPhone(identifier: $request->identifier, password: $request->password);
            $this->authService->setUserFcmToken($user, $request->fcm_token);
            return new AuthUserResource($user);
        } catch (\Exception|NotFoundException $e) {
            return apiResponse(message: $e->getMessage(), code: 422);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return apiResponse(message: __('lang.login success'));
    }

    public function setFcmToken(StoreFcmTokenRequest $request): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $user = auth()->user();
        if (!$user)
            return apiResponse(message: trans('lang.Unauthenticated'));
        $this->authService->setUserFcmToken($user, $request->fcm_token);
        return apiResponse(message: trans('lang.success_operation'));
    }
}
