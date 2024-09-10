<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    use HasFactory;
    protected $fillable=[
        'id',
        'name',
        'email',
        'user_id',
        'product_id',
        'booking_code',
        'status',
        'total_product',
        'total_price'

    ];
}
