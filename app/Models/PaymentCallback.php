<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCallback extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $table = 'investor_payment_callbacks';


    function initializer()
    {

        return $this->belongsTo(PaymentInitializa::class, 'txn_cross_ref', 'txn_ref');
    }
    public function isComplete()
    {
        return ($this->payment_status == 1);
    }
}
