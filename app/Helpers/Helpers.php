<?php

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;

if (!function_exists('sLogger')) {
    /**
     * log specific application errors as thrown.
     *
     * @param  string  $_class
     * @param  string  $description
     * @return bool
     */
    function sLogger(string $_class, string $description)
    {
      //  Log::info('Log::' . $_class . " " . $description);
    }
}


if (!function_exists('uuid')) {
    /**
     * log specific application errors as thrown.
     *
     * @param  string  $_class
     * @param  string  $description
     * @return bool
     */
    function uuid()
    {
        return  Str::uuid()->toString();
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format Kenyan phone numbers into 254 format.
     *
     * @param string $number
     * @param bool $strip_plus
     * @return string
     */
    function format_phone($number, $strip_plus = true)
    {
        // Remove non-digit characters
        $number = preg_replace('/\D+/', '', $number);

        if (Str::startsWith($number, '0')) {
            $number = preg_replace('/^0/', '254', $number);
        } elseif (Str::startsWith($number, '7') || Str::startsWith($number, '1')) {
            $number = '254' . $number;
        } elseif (Str::startsWith($number, '+254')) {
            $number = preg_replace('/^\+254/', '254', $number);
        }

        return $strip_plus ? $number : '+' . $number;
    }
}
if (!function_exists('formatPhoneNumber')) {
    /**
     * get value of phone number according to country and set string length.
     *
     * @param  string  $phone_number
     * @param  string  $code
     * @param  int  $length
     * @return string
     */
    function formatPhoneNumber($number,$strip_plus = true): string
    {
        $number = preg_replace('/\s+/', '', $number);
        $replace = function ($needle, $replacement) use (&$number) {
            if (Str::startsWith($number, $needle)) {
                $pos = strpos($number, $needle);
                $length = \strlen($needle);
                $number = substr_replace($number, $replacement, $pos, $length);
            }
        };
        $replace('0', '+254');
        $replace('7', '+2547');
        $replace('1', '+2541');
        if ($strip_plus) {
            $replace('+254', '254');
        }
        return $number;
    }
}
if (!function_exists('phone_without_prefix')) {
    /**
     * get value of phone number according to country and set string length.
     *
     * @param  string  $phone_number
     * @param  string  $code
     * @param  int  $length
     * @return string
     */
    function phone_without_prefix(string $phone_number, int $length = -9): string
    {
        $number = substr($phone_number, $length);
        return  $number;
    }
}

if (!function_exists('displayGuarator')) {
    /**
     * get listings of property units invoices pending settlement.
     *
     * @param  int  $unit_id
     * @return array
     */
    function displayGuarator($guarantors): array
    {

        return array_filter($guarantors->map(function ($guser) {
            $member = $guser->guser->membership;
            return $member->first_name . ' [' . $member->phone_number . ']';
        })->toArray());
    }
}
if (!function_exists('flexsako_product')) {
    /**
     * get listings of property units invoices pending settlement.
     *
     * @param  int  $unit_id
     * @return array
     */
    function flexsako_product($products): array
    {
        return array_filter($products->map(function ($product) {
            return $product->product_name;
        })->toArray());
    }
}



if (!function_exists('getDays')) {
    function getDays($date)
    {
        return Carbon::parse($date)->diffInDays(Carbon::now(), false)+1;
    }
}


if (!function_exists('getMonths')) {
    function getMonths($date)
    {
        return Carbon::parse($date)->diffInMonths(Carbon::now(), false);
    }
}

if (!function_exists('getYears')) {
    function getYears($date)
    {
        return Carbon::parse($date)->diffInYears(Carbon::now(), false);
    }
}

if (!function_exists('getMinEligibleDays')) {
    function getMinEligibleDays(): int
    {
        return  env('MIN_ELIGIBLE_TIME', 90);
    }
}

if (!function_exists('eligibleLoan')) {
    function eligibleLoan($createdAt): bool
    {
        return  getDays($createdAt) > env('MIN_ELIGIBLE_TIME', 90);
    }
}

if (!function_exists('unlockDays')) {
    function unlockDays($createdAt): int
    {
        return getMinEligibleDays() - getDays($createdAt);
    }
}
if (!function_exists('amount_withdrawable')) {
    function amount_withdrawable($user): float
    {
        $currentWallet = (!$user->hasWallet('current_account') ?
            $user->createWallet(['name' => 'current_account', 'slug' => 'current_account']) :
            $user->getWallet('current_account'));

        if (!$user->hasWallet('saving_wallet')) {
          return 0;
        }
        $savingWallet = $user->getWallet('saving_wallet');
        $creditWallet = $user->getWallet('credit');
        $creditBal = $creditWallet ?$creditWallet->balance :0;


        return (($savingWallet->balance-50)+$creditBal) + interest_amount($user);
    }
}

if (!function_exists('can_withdraw')) {
    function can_withdraw($user): float
    {

      (!$user->hasWallet('current_account') ?
            $user->createWallet(['name' => 'current_account', 'slug' => 'current_account']) :
            $user->getWallet('current_account'));

        $withdrawWallet = $user->getWallet('current_account');


        return (($withdrawWallet->balance-50)) > 0;
    }
}



if (!function_exists('success_response')) {
    /**
     * Get the convert to digit.
     *
     * @param $data
     * @param $message
     * @param $action
     * @return string
     * @internal param string $number
     */
    function success_response($data, $message, $action)
    {
        $response['status'] = 200;
        $response['message'] = $message;
        $response['data'] = $data;
        return response()->json($response, 200);
    }
}

if (!function_exists('error_response')) {

    /**
     * @param $message
     * @param $status
     * @param $action
     * @return \Illuminate\Http\JsonResponse
     */
    function error_response($message, $status, $error_data, $action)
    {
        $response['status'] = $status;
        $response['message'] =   $message;
        !empty($error_data)  ? $response['error_body'] = $error_data : "";
        return response()->json($response, $status);
    }
}

if (!function_exists('is_testing')) {

    /**
     * @param $message
     * @param $status
     * @param $action
     * @return bool
     */
    function is_testing($phoneNumber): bool
    {
        $testers = explode(',', env('DEV_TEST'));
        return in_array($phoneNumber, $testers, false);
    }
}



if (!function_exists('loan_balance')) {

    function loan_balance($phoneNumber): float
    {
        $user = getSessionUser($phoneNumber);
        if ($user) {
            $creditBalance = $user->getWallet('credit')->balance;
            return abs($creditBalance);
        } else {
            return 0;
        }
    }
}

/* This code defines a function `split_value` that takes a string argument `` and replaces any
underscores surrounded by zero or more spaces with a single space. The `if
(!function_exists('split_value'))` statement checks if the function has already been defined before
defining it to avoid redefining the function and causing an error. */
if (!function_exists('split_value')) {

    function split_value($value)
    {
        return preg_replace("/\s*_\s*/", ' ', $value);
    }
}

if (!function_exists('has_product')) {
    /**
     * get listings of property units invoices pending settlement.
     *
     * @param  User $user
     * @return bool
     */
    function has_product($user): bool
    {
        return $user->memberProduct()
            ->ofType('saving')
            ->exists();
    }
}


if (!function_exists('format_date')) {
    /**
     * get listings of property units invoices pending settlement.
     *
     * @param  string  $position
     * @return string
     */
    function format_date($dateString): string
    {
        $date = Carbon::createFromFormat('dmY', $dateString);
        return $date->format('Y-m-d');
    }
}
if (!function_exists('default_email')) {
    /**
     * get listings of property units invoices pending settlement.
     *
     * @param  string  $position
     * @return string
     */
    function default_email($email, $phoneNumber): string
    {
        return  filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : $phoneNumber . '@flesako.com';
    }
}
