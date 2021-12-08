<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonResponse;
use App\Helpers\HandleException;
use App\Helpers\ResponseHelper;
use App\Http\Request\CreateTransactionRequest;
use App\Services\DetailProductService;
use App\Services\OrderService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
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
    ) {
        $this->transactionService = $transactionService;
        $this->orderService = $orderService;
        $this->detailProductService = $detailProductService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send($this->transactionService->findByField('user_id', auth('api')->user()['id'], ['orders', 'orders.product']));
    }

    public function create(CreateTransactionRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = [
                'user_id' => auth('api')->user()['id'] ?? 1,
                'user_name' => $request['user_name'],
                'user_email' => $request['user_email'],
                'user_address' => $request['user_address'],
                'user_phone' => $request['user_phone'],
                'amount' => $request['amount'],
                'payment' => $request['payment'],
                'shipping' => $request['shipping'],
                'message' => $request['message'] ?? null
            ];
            $transaction = $this->transactionService->create($data);
            $orders = [];
            $detailProducts = [];
            $products = $request['products'];
            foreach ($products as $product) {
                $order = [
                    'transaction_id' => $transaction->id ?? null,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'size' => $product['size'],
                    'price' => $product['price']
                ];
                $detailProduct = [
                    'product_id' => $product['id'],
                    'size' => $product['size'],
                    'quantity' => $product['quantity']
                ];
                array_push($orders, $order);
                array_push($detailProducts, $detailProduct);
            }
            $orders = $this->orderService->insert($orders);
            $detailProducts = $this->detailProductService->update($detailProducts);
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

    public function update($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $result = $this->transactionService->update(array('status' => 0), $id);
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
