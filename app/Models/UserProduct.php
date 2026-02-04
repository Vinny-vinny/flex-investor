<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    use HasFactory;

    protected $table = 'investor_user_products';

    protected $guarded = [];

    public function invoice() {
        return $this->hasOne(Invoice::class,'id','invoice_id');
    }
}
