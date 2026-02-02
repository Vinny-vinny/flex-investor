<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            "id_number" => "required|unique:investor_user_details,id_number",
            "email" => "nullable|unique:investor_users,email|email",
        ], [
            "first_name.required" => "First name is required",
            "last_name.required" => "Last name is required",
            "phone_number.required" => "Phone number is required",
            'phone_number.regex' => 'Invalid phone number format. Use 07XXXXXXXX, 01XXXXXXXX, or 2547XXXXXXXX',
            "phone_number.unique" => "Phone number is already registered",
            'dob.date' => 'Date of birth must be a valid date',
            'dob.before' => 'Date of birth must be in the past',
            "dob.required" => "Date of birth is required",
            "id_number.required" => "ID number is required",
            "id_number.unique" => "ID number is already registered",
            "email.unique" => "Email address is already registered",
            "email.email" => "Email address is not valid",
            'gender.required' => 'Gender is required',
            'gender.in' => 'Gender must be male, female, or other',
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
                'dob'          => Carbon::parse($request->dob),
                'gender'       => $request->gender,
                'id_number'    => $request->id_number,
            ]
        );
        return response()->json($userDetails);
    }
}
