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
        return $this->belongsTo(Invoice::class,"product_id");
    }

    public function product() {
        return $this->belongsTo(Product::class,"product_id","id");
    }

    public function user() {
        return $this->belongsTo(User::class,"user_id","id");
    }
}
