<?php

namespace App\Helpers;

use App\Models\Invoice;
use Illuminate\Support\Str;

class InvestorHelper
{

    public static function genReferenceNumber(): string
    {
        $prefix = 'INV52';
        $ref = $prefix . strtoupper(
                substr(
                    str_shuffle('flexinvestornumbergenratrkenyabestfintechflexsackoaddedotthegame'),
                    0,
                    2
                )
                . Str::replaceFirst('O', 'K',
                    Str::replaceFirst('i', 'L', Str::random(3))
                )
                . mt_rand(10000, 99999)
            );

        if (Invoice::where('invoice_number', $ref)->exists()) {
            return self::genReferenceNumber();
        }
        return $ref;
    }
}
