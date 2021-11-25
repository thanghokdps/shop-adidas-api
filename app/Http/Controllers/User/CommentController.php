<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\ResponseHelper;
use App\Http\Request\CreateCommentRequest;
use App\Services\CommentService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index($id): JsonResponse
    {
        return ResponseHelper::send($this->commentService->findByField('product_id', $id));
    }

    public function create(CreateCommentRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = [
                'product_id' => $request['product_id'],
                'user_id' => auth('api')->user()['id'],
                'star' => $request['star'],
                'content' => $request['content'],
            ];
            $comment = $this->commentService->create($data);
            DB::commit();
            return ResponseHelper::send($comment);
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

    public function getAccordingToStar($id, Request $request): JsonResponse
    {
        return ResponseHelper::send($this->commentService->getAccordingToStar($id, $request['star']));
    }

//    public function update($id, CreateCommentRequest $request): JsonResponse
//    {
//        try {
//            DB::beginTransaction();
//            $result = $this->commentService->update($request->all(), $id);
//            DB::commit();
//            return ResponseHelper::send($result);
//        } catch (QueryException $e) {
//            DB::rollBack();
//            Log::error($e);
//            return HandleException::catchQueryException($e);
//        }  catch (Exception $e) {
//            DB::rollBack();
//            Log::error($e);
//            return CommonResponse::unknownResponse();
//        }
//    }
}
