<?php

namespace App\Observers;

use App\Models\InPayment;
use App\Services\WalletService;
use App\Traits\DataTransfer;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;

class InPaymentObserver
{
    use DataTransfer;
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

        //send notifications
        $totalPaid = $this->walletService->getBalance($inPayment->user,$product->slug);
        $payload = [
            "package_name" => $product->product_name,
            "amount" => $inPayment->payment_amount,
            "phone_number" => $inPayment->user->userDetail->phone_number,
            "total_saved" => $totalPaid,
            "balance" => $product->target_amount - $totalPaid,
            "account_number" => $inPayment->invoice->invoice_number,
            "type" => "save"
        ];
        $this->postRequest(env('FLEXSAKO_BASE_URL').'v1/flex-investor/send-sms',$payload);
    }
}
