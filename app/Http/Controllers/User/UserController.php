<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\HttpCode;
use App\Helpers\ResponseHelper;
use App\Helpers\Status;
use App\Http\Request\CreateUserRequest;
use App\Http\Request\DeleteOrUpdateDeletedRequest;
use App\Http\Request\UpdateUserRequest;
use App\Mail\SignupEmail;
use App\Services\UserService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController
{
    protected $userService;
    protected $mailController;

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
            $code = random_int(100000,999999);
            $data = [
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'gender' => $request['gender'],
                'address' => $request['address'],
                'phone' => $request['phone'],
                'code' => $code,
            ];
            $user = $this->userService->create($data);
            DB::commit();
            $this->userService->deleteUsers([$user['id']]);
            $mailData = [
                'send' => 'welcome',
                'code' => $code
            ];
            Mail::to($request['email'])->send(new SignupEmail($mailData));
            return ResponseHelper::send($user);
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
            return CommonResponse::unknownResponse();
        }
    }

    public function forgetPassword(UpdateUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $code = random_int(100000,999999);
            $user = $this->userService->findByEmail( $request['email'])->toArray();
            $result = $this->userService->update(['password'=>Hash::make($request['password']), 'code'=>$code], head($user)['id']);
            DB::commit();
            if(head($user)['deleted_at']==null) {
                $this->userService->deleteUsers([head($user)['id']]);
            }
            $mailData = [
                'send' => 'forget',
                'code' => $code
            ];
            Mail::to($request['email'])->send(new SignupEmail($mailData));
            return ResponseHelper::send(head($user));
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

    public function verify(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        $user = $this->userService->getUserVerify(head($request['ids']));
        if($request['code'] == $user['code']) {
            return ResponseHelper::send($this->userService->updateDeletedUsers($request['ids']));
        } else {
            $errors['auth'] = "Mã xác thực không chính xác!";
            return ResponseHelper::send([], Status::NG, HttpCode::BAD_REQUEST, $errors);
        }
    }
}
