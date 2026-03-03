<?php

namespace App\Http\Controllers;

use App\Helpers\MpesaReader;
use App\Helpers\PaymentsHelper;
use App\Models\InPayment;
use App\Models\Invoice;
use App\Models\UploadedPayment;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{

    public function handleStkCallback(Request $request)
    {
        $data = MpesaReader::stkListener($request->all());
        info(json_encode($request->all()));
        return PaymentsHelper::create($data);
    }

    public function handleC2bCallback(Request $request)
    {
        $data = MpesaReader::c2BListener($request->all());
        info(json_encode($request->all()));
        return PaymentsHelper::create($data);
    }

    public function uploadedPayment(Request $request)
    {
        $phone = format_phone($request->phone_number);

        $validator = Validator::make(array_merge($request->all(), ['phone_number' => $phone]), [
            "amount" => "required|integer|min:1",
            "agent_phone_number" => "required",
            'phone_number' => [
                'required',
                'string',
                //Matches 07XXXXXXXX or 01XXXXXXXX or +2547XXXXXXXX or +2541XXXXXXXX
                'regex:/^(?:\+?254|0)?(7\d{8}|1\d{8})$/',
                'exists:investor_user_details,phone_number',
            ],
            "trans_code" => "required|unique:investor_in_payments,payment_source_txn_id",
            "invoice_number" => "required|exists:investor_invoices,invoice_number",
        ], [
            "phone_number.required" => "Phone number is required",
            'phone_number.regex' => 'Invalid phone number format. Use 07XXXXXXXX, 01XXXXXXXX, or 2547XXXXXXXX',
            "phone_number.exists" => "Phone number doesn't exist",
            "trans_code.required" => "Transaction code is required",
            "trans_code.unique" => "Transaction code is must be unique",
            "invoice_number.required" => "Invoice number is required",
            "invoice_number.exists" => "Invoice number is does not exist",
            "invoice_number.numeric" => "Invoice number must be a number",
            "amount.required" => "Amount is required",
            "amount.numeric" => "Amount must be a number",
            "amount.min" => "Amount must be a number",
            "agent_phone_number.required" => "Agent's phone number is required",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $invoice = Invoice::where("invoice_number", $request->invoice_number)->first();
        $agent = UserDetail::where("phone_number", format_phone($request->agent_phone_number))->first();

        InPayment::create([
            'user_id' => $invoice->user_id,
            'invoice_id' => $invoice->id,
            'payment_amount' => $request->amount,
            'payment_source' => "UPLOADED",
            'payment_source_address' => $phone,
            'payment_source_txn_id' => $request->trans_code,
            'txn_ref'   => $request->invoice_number,
            'created_at'   => $request->payment_date?Carbon::parse($request->payment_date): Carbon::now(),
        ]);

        return UploadedPayment::create([
            'user_id' => $invoice->user_id,
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'uploaded_by' => $agent->user_id,
            'transaction_id' => $request->trans_code,
        ]);

    }
}
