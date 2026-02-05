<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $table = 'investor_in_payments';


    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class,'invoice_id','id');
    }
}
