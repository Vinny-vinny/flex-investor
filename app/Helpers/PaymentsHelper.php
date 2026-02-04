<?php

namespace App\Helpers;

use App\Models\InPayment;
use App\Models\Invoice;
use App\Models\PaymentCallback;
use App\Models\UserDetail;
use App\Models\UserProduct;
use SmoDav\Mpesa\Laravel\Facades\STK;

class PaymentsHelper
{

    public static function create(array $data, $callbackData=[])
    {
        if (!isset($data['txn_code'])) {
            info("txn_code is null ".json_encode($data));
            return false;
        }

        $callB = PaymentCallback::where('txn_code', $data['txn_code'])->first();
        if ($callB) {
            return self::updateIn($callB);
        }

        $paymentData = [
            "phone_number" => $data['phone_number'],
            "txn_code" => $data['txn_code'],
            "amount" => $data['amount'],
            "invoice_number" => $data['txn_cross_ref'],
        ];

        self::insertPayment($paymentData);
    }

    public static function updateIn(PaymentCallback $callback)
    {
        STK::validate($callback->txn_converstion_id);
        $callback->update(['payment_status'=>1]);

            if (isset($callback->initializer)) {
                $user_id = $callback->initializer->user_id;
                $phone = $callback->initializer->phone_number;
                $invoice_id = $callback->initializer->invoice_id;
            }else {
                $member = UserDetail::where('phone_number',format_phone($callback->txn_cross_ref))->first();
                $user_id = $member->user_id;
                $phone = $callback->txn_cross_ref;
                $invoice_id = rand(10000,900000);
            }

           return InPayment::create([
                'user_id' => $user_id,
                'invoice_id' => $invoice_id,
                'payment_amount' => $callback->amount,
                'payment_source' => $callback->payment_type,
                'payment_source_address' => $phone,
                'payment_source_txn_id' => $callback->txn_code,
                'txn_ref'   => $callback->txn_cross_ref
            ]);

            // communicate Works payment
    }

    public static function insertPayment($data)
    {
        $member = UserDetail::where('phone_number',$data['phone_number'])->first();
        if ($member) {
            $invoice = Invoice::where('invoice_number', $data["invoice_number"])->first();

            if (!$invoice) {
                $memberProduct = UserProduct::where('user_id', $member->user_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                if ($memberProduct) {
                    $invoice =   $memberProduct->invoice;
                }
            }
            if (!$invoice) {
                info("Invoice not found ".json_encode($data));
                return;
            }

            return InPayment::create([
                'user_id' => $member->user_id,
                'invoice_id' =>$invoice->id,
                'payment_amount' => $data['amount'],
                'payment_source' => 'M-PESA',
                'payment_source_address' => $data['phone_number'],
                'payment_source_txn_id' => $data['txn_code'],
                'txn_ref' => $invoice->invoice_number,
            ]);
        }
    }


}
