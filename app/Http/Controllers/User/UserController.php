<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\ResponseHelper;
use App\Http\Request\CreateUserRequest;
use App\Http\Request\UpdateUserRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send(auth('api')->user());
    }

    public function create(CreateUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'gender' => $request['gender'],
                'address' => $request['address'],
                'phone' => $request['phone'],
            ];
            $transaction = $this->userService->create($data);
            DB::commit();
            return ResponseHelper::send($transaction);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return HandleException::catchQueryException($e);
        }  catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return CommonResponse::unknownResponse($e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $userId = auth('api')->user()['id'];
            $result = $this->userService->update($request->all(), $userId);
            DB::commit();
            return ResponseHelper::send($result);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return HandleException::catchQueryException($e);
        }  catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return CommonResponse::unknownResponse($e->getMessage());
        }
    }
}
