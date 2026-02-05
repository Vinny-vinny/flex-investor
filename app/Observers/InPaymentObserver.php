<?php

namespace App\Observers;

use App\Models\InPayment;
use App\Services\WalletService;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;

class InPaymentObserver
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * @throws ExceptionInterface
     */
    public function created(InPayment $inPayment)
    {
        $product = $inPayment->invoice->product;
        $this->walletService->credit($inPayment->user, $product->slug, $inPayment->payment_amount, $inPayment->payment_source_txn_id);
    }
}
