<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController
{

    public function getUserByPhone($phone)
    {
        return response()->json(UserDetail::where('phone_number', format_phone($phone))->first());
    }

    public function onboardUser(Request $request)
    {
        $phone = format_phone($request->phone_number);

        $validator = Validator::make(array_merge($request->all(), ['phone_number' => $phone]), [
            "first_name" => "required",
            "last_name" => "required",
            'phone_number' => [
                'required',
                'string',
                // Matches 07XXXXXXXX or 01XXXXXXXX or +2547XXXXXXXX or +2541XXXXXXXX
                'regex:/^(?:\+?254|0)?(7\d{8}|1\d{8})$/',
                'unique:investor_user_details,phone_number',
            ],
            "id_number" => "required|unique:investor_user_details,id_number",
         ], [
            "first_name.required" => "First name is required",
            "last_name.required" => "Last name is required",
            "phone_number.required" => "Phone number is required",
            'phone_number.regex' => 'Invalid phone number format. Use 07XXXXXXXX, 01XXXXXXXX, or 2547XXXXXXXX',
            "phone_number.unique" => "Phone number is already registered",
            "id_number.required" => "ID number is required",
            "id_number.unique" => "ID number is already registered",
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::firstOrCreate(["email" => $request->email?:$request->phone_number."@gmail.com"],[
            "password" => bcrypt($request->phone_number)
        ]);

        $userDetails = $user->userDetail()->updateOrCreate(
            ['phone_number' => format_phone($request->phone_number)],
            [
                'phone_number' => format_phone($request->phone_number),
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'dob'          => $request->dob ?:Carbon::parse($request->dob),
                'gender'       => $request->gender,
                'id_number'    => $request->id_number,
            ]
        );
        return response()->json($userDetails);
    }

    public function promoterSignup(Request $request)
    {
        $phone = format_phone($request->phone_number);
        $validator = Validator::make(array_merge($request->all(), ['phone_number' => $phone]), [
            'promoter_data' => 'required|array',
            'product_id' => 'required|integer|exists:investor_products,id',
            'phone_number' => [
                'required',
                'string',
                //Matches 07XXXXXXXX or 01XXXXXXXX or +2547XXXXXXXX or +2541XXXXXXXX
                'regex:/^(?:\+?254|0)?(7\d{8}|1\d{8})$/',
                'unique:investor_user_details,phone_number',
            ],
        ], [
            'product_id.integer' => 'Product id must be an integer',
            'product_id.exists' => 'Product id must be an integer',
            'product_id.required' => 'Product id is required',
            "phone_number.required" => "Phone number is required",
            'phone_number.regex' => 'Invalid phone number format. Use 07XXXXXXXX, 01XXXXXXXX, or 2547XXXXXXXX',
            "phone_number.unique" => "Phone number is already registered",
            'promoter_data.required' => 'Promoter data is required',
            'promoter_data.array' => 'Promoter data must be an array',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $request['deposit_amount'] = Product::find($request->product_id)->base_amount;

        $promoter_data = $request->promoter_data;
        //agent onboarding details
        if ($promoter_data) {
            $phone = format_phone($promoter_data['phone_number']);

            // First check if a user already exists with this phone
            $existingUser = User::whereHas('userDetail', function ($q) use ($phone) {
                $q->where('phone_number', $phone);
            })->first();

            if ($existingUser) {
                $romoterUser = $existingUser;
            } else {
                $romoterUser = User::create([
                    "email" => $promoter_data['email'] ?: $phone . "@gmail.com",
                    "password" => bcrypt($phone)
                ]);
            }

            $romoterUser->userDetail()->updateOrCreate(
                ['phone_number' => $phone],
                [
                    'first_name' => $promoter_data['first_name'],
                    'last_name' => $promoter_data['last_name'],
                    'dob' => $promoter_data['dob'] ?: Carbon::parse("1990-01-01"),
                    'gender' => "male",
                    'id_number' => $promoter_data['id_number']
                ]
            );
            $romoterUser->agent()->updateOrCreate(['user_id' => $romoterUser->id], []);
        }
        //customer onboarding
        $phone = format_phone($request->phone_number);

        // Check if user already exists with this phone
        $user = User::whereHas('userDetail', function ($q) use ($phone) {
            $q->where('phone_number', $phone);
        })->first();

        if (!$user) {
            $user = User::create([
                "email" => $request->email ?: $phone . "@gmail.com",
                "password" => bcrypt($phone),
                "agent_id" => $romoterUser->id ?? null,
            ]);
        }

        $userDetails = $user->userDetail()->updateOrCreate(
            ['phone_number' => $phone],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $request->dob
                    ? Carbon::parse($request->dob)
                    : Carbon::parse("1990-01-01"),
                'gender' => $request->gender,
                'id_number' => $request->id_number,
            ]
        );
        $join_data = [
            "phone_number" => format_phone($request->phone_number),
            "product_id" => $request->product_id,
            "deposit_amount" => Product::find($request->product_id)->base_amount,
        ];

        $joinRequest = (new Request())->merge($join_data);
        app(ProductsController::class)->join($joinRequest);
        return response()->json($userDetails);
    }
}
