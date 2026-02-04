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
}
