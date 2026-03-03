<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadedPayment extends Model
{
    use HasFactory, softDeletes;

    protected $table = "investor_uploaded_payments";

    protected $guarded = [];
}
