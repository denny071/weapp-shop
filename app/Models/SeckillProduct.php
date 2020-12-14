<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeckillProduct extends Model
{

    protected $fillable = ['start_at', 'end_at'];

    // 会自动转为 Carbon 类型
    protected $dates = ['start_at','end_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
