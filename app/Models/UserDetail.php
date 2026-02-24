<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $table = 'investor_user_details';

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

}
