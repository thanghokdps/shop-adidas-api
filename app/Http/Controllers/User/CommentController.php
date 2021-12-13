<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\ResponseHelper;
use App\Helpers\Status;
use App\Http\Request\CreateCommentRequest;
use App\Services\CommentService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            $bad_words = array('cc', 'ccc', 'cccc', 'concet', 'cailon', 'cailoz', 'lon', 'loz', 'clgt','clmm', 'cmm', 'djtme', 'ditme', 'dit', 'djt', 'koncet', 'cet'
            ,'vcl','cl','xxx','mm','đcmm','đmm','đm','địt','đụ','đĩ','lồn','cẹt','kẹt','mẹ mày','thèn cha mày', 'cứt', 'cut');
            if ($this->strpos_arr($request['content'], $bad_words)) {
                return ResponseHelper::send(['error'=>'Vui lòng không nói tục, chửi thề']);
            } else {
                $data = [
                    'product_id' => $request['product_id'],
                    'user_id' => auth('api')->user()['id'],
                    'star' => $request['star'],
                    'content' => $request['content'],
                ];
                $comment = $this->commentService->create($data);
                if(isset($request['image'])) {
                    $imageList = $request['image'];
                    $imageUrlsArr = [];
                    foreach ($imageList as $image) {
                        $imageName = $image->getClientOriginalName();
                        $pathImage = "comment/" . $comment->id . "/$imageName";
                        Storage::disk("public")->put($pathImage, file_get_contents($image));
                        array_push($imageUrlsArr,$pathImage);
                    }
                    $imageUrls = implode(';', $imageUrlsArr);
                    $comment = $this->commentService->update(["image" => $imageUrls], $comment->id);
                }
            }
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

    public function strpos_arr($haystack, $needle): bool
    {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $what) {
            if (strpos($haystack, $what) !== FALSE) return TRUE;
        }
        return false;
    }
}
