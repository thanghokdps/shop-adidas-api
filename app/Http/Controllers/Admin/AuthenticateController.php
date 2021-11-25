<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ErrorCodeHelper;
use App\Helpers\HttpCode;
use App\Helpers\ResponseHelper;
use App\Helpers\Status;
use App\Http\Controllers\Controller;
use App\Http\Request\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthenticateController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api-admin')->attempt($credentials)) {
            $errors['auth'] = ErrorCodeHelper::UNAUTHORIZED;

            return ResponseHelper::send([], Status::NG, HttpCode::UNAUTHORIZED, $errors);
        }
        return ResponseHelper::send(['token' => $token, 'info' => auth('api-admin')->user()]);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return ResponseHelper::send([]);
    }
}
