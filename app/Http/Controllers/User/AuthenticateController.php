<?php

namespace App\Http\Controllers\User;

use App\Helpers\ErrorCodeHelper;
use App\Helpers\HttpCode;
use App\Helpers\ResponseHelper;
use App\Helpers\Status;
use App\Http\Request\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthenticateController
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            $errors['auth'] = "Tài khoản hoặc mật khẩu không đúng";
            return ResponseHelper::send([], Status::NG, HttpCode::UNAUTHORIZED, $errors);
        }
        return ResponseHelper::send(['token' => $token, 'info' => auth('api')->user()]);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return ResponseHelper::send([]);
    }
}
