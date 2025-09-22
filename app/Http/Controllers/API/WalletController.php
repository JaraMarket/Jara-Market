<?php

namespace App\Http\Controllers\API;


use App\Services\PaymentGateways\Paystack;
use Exception;
use Illuminate\Http\Response;
use App\Http\Requests\Paystack\TransferToBankRequest;
use App\Http\Controllers\Controller;
use App\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}
    
    public function transfer(TransferToBankRequest $request)
    {
        try {
            $user = $request->user();
            $data = $request->validated();


            $result = $this->walletService->transferToBank(
                $user,
                $data
            );
    
            return response()->success('Transfer initiated successfully', $result, 200);
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], 500);
        }
    }
    
}
