<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\ResponseHelper;
use App\Http\Request\CreateProductRequest;
use App\Http\Request\DeleteOrUpdateDeletedRequest;
use App\Http\Request\UpdateProductRequest;
use App\Services\ProductService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send($this->productService->all());
    }

    public function create(CreateProductRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request['name'],
                'category_id' => $request['category_id'],
                'price' => $request['price'],
                'description' => $request['description'],
                'specifications' => $request['specifications'],
            ];
            $product = $this->productService->create($data);
            $imageMain = $request['image'];
            $imageList = $request['image_list'];
            $imageUrlsArr = [];
            foreach ($imageList as $image) {
                $imageName = $image->getClientOriginalName();
                $pathImage = "product/" . $product->id . "/$imageName";
                Storage::disk("public")->put($pathImage, file_get_contents($image));
                array_push($imageUrlsArr,$pathImage);
            }
            $imageMainName = $imageMain->getClientOriginalName();
            $pathImageMain = "product/" . $product->id . "/$imageMainName";
            Storage::disk("public")->put($pathImageMain, file_get_contents($imageMain));
            $imageUrls = implode(';', $imageUrlsArr);
            $product = $this->productService->update(["image" => $pathImageMain, "image_list" => $imageUrls], $product->id);
            DB::commit();
            return ResponseHelper::send($product);
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

    public function update($id, UpdateProductRequest $request): JsonResponse
    {
        if(isset($request['image'])) {
            $imageMain = $request['image'];
            $imageMainName = $imageMain->getClientOriginalName();
            $pathImageMain = "product/" . $id . "/$imageMainName";
            Storage::disk("public")->put($pathImageMain, file_get_contents($imageMain));
            $this->productService->update(["image" => $pathImageMain], $id);
        }
        if (isset($request['image_list']) ) {
            $imageList = $request['image_list'];
            $imageUrlsArr = [];
            foreach ($imageList as $image) {
                $imageName = $image->getClientOriginalName();
                $pathImage = "product/" . $id . "/$imageName";
                Storage::disk("public")->put($pathImage, file_get_contents($image));
                array_push($imageUrlsArr,$pathImage);
            }
            $imageUrls = implode(';', $imageUrlsArr);
            $result = $request['image_list_string']."; ".$imageUrls;
            $this->productService->update(["image_list" => $result], $id);
        }
        if (isset($request['image_list_string']) && !isset($request['image_list'])) {
            $this->productService->update(["image_list" => $request['image_list_string']], $id);
        }
        try {
            DB::beginTransaction();
            $result = $this->productService->update($request->except('image', 'image_list', 'image_list_string'), $id);
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

    public function deleteProducts(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->productService->deleteProducts($request['ids']));
    }

    public function getDeletedProducts(): JsonResponse
    {
        return ResponseHelper::send($this->productService->getDeletedProducts());
    }

    public function updateDeletedProducts(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->productService->updateDeletedProducts($request['ids']));
    }

    public function search(Request $request): JsonResponse
    {
        return ResponseHelper::send($this->productService->search($request['name']));
    }
}
