<?php

namespace App\Http\Controllers;

use App\Helpers\InvestorHelper;
use App\Http\Resources\ProductsResource;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\UserDetail;
use App\Models\UserProduct;
use App\Services\PaymentGatewayService;
use App\Services\WalletService;
use App\Traits\DataTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    use DataTransfer;
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }
    public function index()
    {
        return response()->json(ProductsResource::collection(Product::all()));
    }

    public function join(Request $request)
    {
        $phone = format_phone($request->phone_number);
        $validator = Validator::make(array_merge($request->all(), ['phone_number' => $phone]), [
            'product_id' => 'required|integer|exists:investor_products,id',
            'phone_number' =>  "required|exists:investor_user_details,phone_number",
            'deposit_amount' => 'required|integer|min:1',
        ], [
            'product_id.integer' => 'Product id must be an integer',
            'product_id.exists' => 'Product id must be an integer',
            'product_id.required' => 'Product id is required',
            'phone_number.required' => 'Phone number is required',
            'phone_number.regex' => 'Invalid phone number format. Use 07XXXXXXXX, 01XXXXXXXX, or 2547XXXXXXXX',
            'phone_number.exists' => 'User with the provided phone number is not found.',
            'deposit_amount.required' => 'Deposit amount is required',
            'deposit_amount.integer' => 'Deposit amount must be an integer',
            'deposit_amount.min' => 'Deposit amount must be at least 1'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if user is found by phone
        $user = UserDetail::where('phone_number', format_phone($request->phone_number))->first();
        if (!$user) {
            return response()->json("Phone number not found", 404);
        }

        $invoice = Invoice::firstOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
            ],
            [
                'invoice_number' => InvestorHelper::genReferenceNumber(),
            ]
        );

        $product = UserProduct::updateOrCreate([
            'user_id' => $user->id,
            'product_id' => $request->product_id
        ], [
                'enrollment_date' => Carbon::now(),
                'deadline' => Carbon::now()->addYear()
            ]
        );

        //create wallet here
        $this->walletService->createWallet($invoice->user, $invoice->product->product_name, $invoice->product->slug);
        PaymentGatewayService::charge($user->user, $invoice->invoice_number, $request->deposit_amount, $phone);

        //send notifications
         $payload = [
            "package_name" => $invoice->product->product_name,
            "next_deposit_amount" => $invoice->product->base_amount,
            "phone_number" => $invoice->user->userDetail->phone_number,
            "constant_weekly" => round($invoice->product->target/52),
            "constant_monthly" => round($invoice->product->target/12),
            "type" => "onboard"
        ];

        $this->postRequest(env('FLEXSAKO_BASE_URL').'v1/flex-investor/send-sms',$payload);
        return response()->json($product);
    }

}
