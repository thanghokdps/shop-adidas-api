<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\HttpCode;
use App\Helpers\ResponseHelper;
use App\Helpers\Status;
use App\Http\Request\CreateUserRequest;
use App\Http\Request\DeleteOrUpdateDeletedRequest;
use App\Http\Request\UpdateUserRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
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
        return ResponseHelper::send($this->userService->all());
    }

    public function create(CreateUserRequest $request): JsonResponse
    {
        if(!isset($request['email'])) {
            return ResponseHelper::send([], Status::NG, HttpCode::BAD_REQUEST, ['email' => 'The email field is required.']);
        }
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
            return CommonResponse::unknownResponse();
        }
    }

    public function update($id, UpdateUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $result = $this->userService->update($request->all(), $id);
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

    public function deleteUsers(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->userService->deleteUsers($request['ids']));
    }

    public function getDeletedUsers(): JsonResponse
    {
        return ResponseHelper::send($this->userService->getDeletedUsers());
    }

    public function updateDeletedUsers(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->userService->updateDeletedUsers($request['ids']));
    }
}
