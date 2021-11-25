<?php

namespace App\Http\Middleware;

use App\Helpers\ErrorCodeHelper;
use App\Helpers\HttpCode;
use App\Helpers\ResponseHelper;
use App\Helpers\Status;
use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Request;

class UserAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (auth('api')->authenticate()) {
                return $next($request);
            } else {
                return ResponseHelper::send(
                    [],
                    Status::NG,
                    HttpCode::UNAUTHORIZED,
                    ['auth' => ErrorCodeHelper::UNAUTHORIZED]
                );
            }
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return ResponseHelper::send(
                    [],
                    Status::NG,
                    HttpCode::UNAUTHORIZED,
                    ['jwt_token_invalid' => ErrorCodeHelper::INVALID_JWT_TOKEN]
                );

            } else if ($e instanceof TokenExpiredException) {
                return ResponseHelper::send(
                    [],
                    Status::NG,
                    HttpCode::UNAUTHORIZED,
                    ['jwt_token_expired' => ErrorCodeHelper::EXPIRED_JWT_TOKEN]
                );
            } else {
                return ResponseHelper::send(
                    [],
                    Status::NG,
                    HttpCode::UNAUTHORIZED,
                    ['jwt_mdlw_error' => $e->getMessage()]
                );
            }
        }
    }
}
