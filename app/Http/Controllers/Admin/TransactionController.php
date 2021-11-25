<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\ResponseHelper;
use App\Services\DetailProductService;
use App\Services\OrderService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController
{
    protected $transactionService;
    protected $orderService;
    protected $detailProductService;
    public function __construct(
        TransactionService $transactionService,
        OrderService $orderService,
        DetailProductService $detailProductService
    ){
        $this->transactionService = $transactionService;
        $this->orderService = $orderService;
        $this->detailProductService = $detailProductService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send($this->transactionService->all());
    }

    public function getDetail($id): JsonResponse
    {
        return ResponseHelper::send($this->transactionService->findByField('user_id', $id, ['orders', 'orders.product']));
    }

    public function update($id, Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            if($request['status'] == 0){
                $result = $this->transactionService->update($request['status'], $id);
                $orders = $this->orderService->findByField('transaction_id', $id);
                $detailProducts = [];
                foreach ($orders as $order)
                {
                    $detailProduct = [
                        'product_id' => $order['product_id'],
                        'size' => $order['size'],
                        'quantity' => -$order['quantity']
                    ];
                    array_push($detailProducts, $detailProduct);
                }
                $detailProducts = $this->detailProductService->update($detailProducts);
                $this->orderService->delete($id);
            } else {
                $result = $this->transactionService->update($request['status'], $id);
            }
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
