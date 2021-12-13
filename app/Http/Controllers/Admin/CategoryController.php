<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\HttpCode;
use App\Helpers\ResponseHelper;
use App\Helpers\Status;
use App\Http\Request\CreateCategoryRequest;
use App\Http\Request\DeleteOrUpdateDeletedRequest;
use App\Http\Request\UpdateCategoryRequest;
use App\Services\CategoryService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController
{
    protected $categoryService;
    protected $productService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = array_filter(
            $this->categoryService->all()->toArray(),
            function ($var) {
                return is_null($var['type']);
            }
        );
        foreach($categories as $key => $category) {
            $subs = array_filter(
                $this->categoryService->all()->toArray(),
                function ($var) use ($category) {
                    return $var['type'] == $category['id'];
                }
            );
            $categories[$key]['subs'] = array_values($subs);
        }
        return ResponseHelper::send(array_values($categories));
    }

    public function create(CreateCategoryRequest $request): JsonResponse
    {
        if(isset($request['type']))
        {
            $categoriesArray = array_filter(
                $this->categoryService->all()->toArray(),
                function ($var) {
                    return is_null($var['type']);
                }
            );
            $categoriesTypeNullId = collect($categoriesArray)->pluck('id');
            if(!in_array($request['type'], $categoriesTypeNullId->toArray()))
            {
                return ResponseHelper::send([], Status::NG, HttpCode::BAD_REQUEST, ['type' => 'The type field is wrong.']);
            }
        }
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request['name'],
                'type' => $request['type'],
            ];
            $category = $this->categoryService->create($data);
            DB::commit();
            return ResponseHelper::send($category);
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

    public function update($id, UpdateCategoryRequest $request): JsonResponse
    {
        $subs = $this->categoryService->findByField("type", $id);
        if($subs->isNotEmpty() && isset($request['type'])) {
            return ResponseHelper::send(["error" => "Category cannot update type"], Status::NG);
        }
        try {
            DB::beginTransaction();
            $result = $this->categoryService->update($request->all(), $id);
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

    public function deleteCategories(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->categoryService->deleteCategories($request['ids']));
    }

    public function getDeletedCategories(): JsonResponse
    {
        return ResponseHelper::send($this->categoryService->getDeletedCategories());
    }

    public function updateDeletedCategories(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->categoryService->updateDeletedCategories($request['ids']));
    }
}
