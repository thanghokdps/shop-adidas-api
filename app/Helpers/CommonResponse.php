<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class CommonResponse {
    public static function notFoundResponse(): JsonResponse
    {
        return ResponseHelper::send(
            [],
            Status::NG,
            HttpCode::NOT_FOUND,
            ErrorCodeHelper::NOT_FOUND
        );
    }

    public static function existedResponse(): JsonResponse
    {
        return ResponseHelper::send(
            [],
            Status::NG,
            HttpCode::CONFLICT,
            ErrorCodeHelper::EXISTED
        );
    }

    public static function invalidResponse(): JsonResponse
    {
        return ResponseHelper::send(
            [],
            Status::NG,
            HttpCode::BAD_REQUEST,
            ErrorCodeHelper::INVALID
        );
    }

    public static function forbiddenResponse(): JsonResponse
    {
        return ResponseHelper::send(
            [],
            Status::NG,
            HttpCode::FORBIDDEN,
            ErrorCodeHelper::NOT_ALLOW
        );
    }

    public static function unknownResponse($e): JsonResponse
    {
        if (str_contains($e, 'No query results')) {
            $e = 'Value is not found';
        }
        return ResponseHelper::send(["error" => $e], Status::NG);
    }
}
