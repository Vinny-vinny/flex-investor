<?php

namespace App\Http\Controllers;

use App\Helpers\MpesaReader;
use App\Helpers\PaymentsHelper;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{

    public function handleC2bCallback(Request $request)
    {
        $data = MpesaReader::stkListener(json_encode($request->all()));
        info(json_encode($request->all()));
        return PaymentsHelper::create($data,json_encode($request->all()));
    }
}
