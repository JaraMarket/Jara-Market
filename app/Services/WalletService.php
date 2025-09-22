<?php

namespace App\Services;

use App\Services\PaymentGateways\Paystack;
use App\Models\BankAccount;
use App\Models\User;
use App\Enums\WalletTransactionTypeEnum;
use App\Notifications\WalletNotification;
use App\Services\TransactionLogService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WalletService
{
    public function __construct(
        protected TransactionLogService $transactionLogService, 
        protected Paystack $paystack
    ) {}

    public function transferToBank(User $user, array $data)
    {
        if ($user->wallet->balance < $data['amount']) {
            throw new Exception('Insufficient balance.');
        }

        try {
            return DB::transaction(function () use ($user, $data) {
                $bankAccount = $user->bankAccounts()
                ->where('id', $data['bank_id'])
                ->firstOrFail();

                // Initiate payout via Paystack service
                $transfer = $this->paystack->payout(
                    bank_account: $bankAccount,
                    amount: $data['amount'],
                    owner_id: $user->id,
                    owner_type: get_class($user)
                );
                
                // Log debit transaction
                $this->transactionLogService::debit(
                    $user->id,
                    get_class($user),
                    $data['amount'],
                    null,
                    null,
                    $data['currency'],
                    $data['remark']
                );

                return $transfer;
            });
        } catch (Exception $e) {
            // Optionally log the error here
            throw new Exception("Wallet transfer failed: " . $e->getMessage());
        } 
    }
}
