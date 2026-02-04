<?php

namespace App\Helpers;

use App\Models\PaymentInitializa;
use Carbon\Carbon;

class MpesaReader
{
    /**
     * It takes the json data from the callback url and decodes it to an object.
     *
     * @param jsonData The JSON data received from the callback.
     *
     * @return An array of data that is to be saved in the database.
     */
    public static function stkListener($data): array
    {
        info("ggg ".json_encode($data));
        $init = PaymentInitializa::where('txn_converstion_id',$data->CheckoutRequestID)->first();
        info("iinit ".json_encode($data));
        $responseData = [
            'originator_conversation_id' => $data->MerchantRequestID,
            'txn_converstion_id' => $data->CheckoutRequestID,
            'result_description' => $data->ResultDesc,
            'payment_type' => 'M-PESA',
            'txn_cross_ref' => $init->txn_ref,
            'payment_type_id' => env('ACTIVE_PAYBILL', 4101015),
            'result_code' => $data->ResultCode,
            'amount' => 0,
            'txn_code' => $data->CheckoutRequestID,
            'balance_amount' => 0
        ];
        if (intval($data->ResultCode) == 0) {
            $callbackMetadata = $data->CallbackMetadata->Item;
            foreach ($callbackMetadata as $callback) {
                if ($callback->Name == 'Amount') {
                    $responseData['amount'] = @$callback->Value;
                }
                if ($callback->Name == 'MpesaReceiptNumber') {
                    $responseData['txn_code'] = @$callback->Value;
                }
                if ($callback->Name == 'Balance') {
                    $responseData['balance_amount'] = doubleval(@$callback->Value);
                }
                if ($callback->Name == 'TransactionDate') {
                    $responseData['transaction_date'] = @$callback->Value;
                }
                if ($callback->Name == 'PhoneNumber') {
                    $responseData['phone_number'] = @$callback->Value;
                }
            }
        }
        return $responseData;
    }

    /**
     * It returns the data in the format that the system expects.
     *
     * @param jsonData The json data received from the callback url.
     *
     * @return an array of data that is used to update the database.
     */
    public static function uploadListener($jsonData): array
    {

        $data = json_decode($jsonData, true);
        return (array)[
            'result_code' => 0,
            'result_description' => 'success',
            'txn_code' => $data['transid'],
            'amount' => $data['transamount'],
            'txn_cross_ref' => $data['billrefnumber'],
            'balance_amount' => $data['orgaccountbalance'],
            'transaction_date' => Carbon::parse($data['transtime']),
            'payment_type' => 'M-PESA',
            'payment_type_id' => env('ACTIVE_PAYBILL', 4101015)
        ];
    }

    /**
     * It returns the data in the format that the system expects.
     *
     * @param jsonData The json data received from the callback url.
     *
     * @return an array of data that is used to update the database.
     */
    public static function c2BListener($jsonData): array
    {
        $data = json_decode($jsonData, true);
        return (array)[
            'result_code' => 0,
            'result_description' => 'success',
            'txn_code' => $data['TransID'],
            'amount' => $data['TransAmount'],
            'txn_cross_ref' => $data['BillRefNumber'],
            'balance_amount' => $data['OrgAccountBalance'],
            'transaction_date' => $data['TransTime'],
            'payment_type' => 'M-PESA',
            'phone_number' => self::getUserByHashedPhoneAndName($data['MSISDN'],$data['FirstName'],$data['BillRefNumber'])->phone_number,
            'payment_type_id' => env('ACTIVE_PAYBILL', 4101015)
        ];
    }
    public static function getUserByHashedPhoneAndName($phone, $firstName, $reference)
    {
        $userByReference = self::getPhoneByReference($reference);
        if ($userByReference) {
            return $userByReference;
        }

        $startingDigits = substr($phone, 0,4); // get first 4 digits
        $endingDigits = substr($phone, -3); // get last 3 digits

        $user = MembershipDetail::where('phone_number', 'like', $startingDigits . '%')
            ->where('phone_number', 'like', '%' . $endingDigits)
            ->where('first_name', 'like', '%' . $firstName . '%')
            ->first();

        // Optional: return fallback user if not found
        return $user ?? MembershipDetail::where('phone_number', '254704522671')->first();
    }

    public static function getPhoneByReference($reference)
    {
        $invoice = MembershipInvoice::where('invoice_number',$reference)->first();

        if ($invoice && isset($invoice->user->membership)) {
            return $invoice->user->membership;
        }
        $phoneFormatted = formatPhoneNumber($reference);
        return MembershipDetail::where('phone_number', $phoneFormatted)->first();
    }
    /**
     * It takes a json string and returns an array
     *
     * @param jsonData The json data returned from the M-PESA API
     *
     * @return an array of the following:
     */
    public static function b2CListener($jsonData)
    {
        $data = json_decode($jsonData, true);
        return [
            'result_code' => $data['ResultCode'],
            'result_description' => $data['ResultDesc'],
            'txn_code' => $data['TransactionID'],
            'amount' => $data['TransAmount'],
            'txn_cross_ref' => $data['BillRefNumber'],
            'balance_amount' => $data['OrgAccountBalance'],
            'transaction_date' => $data['TransTime'],
            'originator_conversation_id' => $data['OriginatorConversationID'],
            'txn_converstion_id' => $data['ConversationID'],
            'payment_type' => 'M-PESA',
            'payment_type_id' => env('ACTIVE_PAYBILL', 4101015)
        ];
    }
}
