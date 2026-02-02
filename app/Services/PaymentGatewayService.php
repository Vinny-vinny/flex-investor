<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentInitializa;
use App\Models\User;
use SmoDav\Mpesa\Laravel\Facades\STK;

class PaymentGatewayService
{
    public static function charge(User $user, $requestId, $amount, $phone = "", $description = "FlexInvestor Payment")
    {
        info('----- '.$phone);
        if ($phone) {
            $phoneNumber = $phone;
        } else {
            $phoneNumber = $user->userDetail->phone_number;
        }
        info("===== ".$phoneNumber);
        $response = STK::request((int)$amount)
            ->from($phoneNumber)
            ->usingReference($requestId, $description)
            ->push();

        info(json_encode($response));

        if (isset($response->ResponseCode) && $response->ResponseCode == 0) {
            $invoice = Invoice::where('invoice_number',$requestId)->first();
            PaymentInitializa::create([
                'user_id' => $user->id,
                'invoice_id' => optional($invoice)->id,
                'phone_number' => $phoneNumber,
                'amount' => $amount,
                'description' => $description,
                'txn_ref' => $requestId,
                'txn_type' => 'c2b',
                'txn_converstion_id' => $response->CheckoutRequestID,
                'originator_conversation_id' => $response->MerchantRequestID,
                'payment_type' => 'M-PESA'
            ]);
        }
    }
}
