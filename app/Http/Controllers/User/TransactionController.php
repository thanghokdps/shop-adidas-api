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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'image' => $product['image'],
                    'size' => $product['size'],
                    'price' => $product['price'],
                    'created_at' => Carbon::now()
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

            if ($request['payment'] == "Thanh toán trực tuyến")
            {
                $vnpUrl = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
                $vnpReturnUrl = "http://localhost:3000/payment";
                $vnpTmnCode = "AQHHUE0M";
                $vnpHashSecret = "WBCBRJDBFAFQDJAWXBQKJJXPJZCHVOTH";
                $vnpTxnRef = $transaction->id;
                $vnpAmount = $request['amount']*100;
                $vnpOrderInfo = 'Thanh toan don hang';
                $vnpOrderType = 'billpayment';
                $vnpLocale = 'vn';
                $vnpBankCode = 'NCB';
                $vnpIpAdd = request()->ip();

                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnpTmnCode,
                    "vnp_Amount" => $vnpAmount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnpIpAdd,
                    "vnp_Locale" => $vnpLocale,
                    "vnp_OrderInfo" => $vnpOrderInfo,
                    "vnp_OrderType" => $vnpOrderType,
                    "vnp_Returnurl" => $vnpReturnUrl,
                    "vnp_TxnRef" => $vnpTxnRef,
                    "vnp_BankCode" => $vnpBankCode,
                );
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashData = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashData .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnpUrl = $vnpUrl . "?" . $query;
                $vnpSecureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);
                $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
                return ResponseHelper::send(['vnp' => $vnpUrl]);
            }
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

    public function update($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $result = $this->transactionService->update(['status' => 0], $id);
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

    public function returnPayment(Request $request): JsonResponse
    {
        $orders = $this->orderService->findByField('transaction_id', $request['vnp_TxnRef']);
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
        $this->orderService->deleteHard($request['vnp_TxnRef']);
        $this->transactionService->delete($request['vnp_TxnRef']);
        return ResponseHelper::send(['status' => true]);
    }
}
