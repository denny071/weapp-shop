<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'province',
        'province_code',
        'city',
        'city_code',
        'district',
        'district_code',
        'address',
        'is_default',
        'contact_name',
        'contact_phone',
        'last_used_at'

    ];

}
