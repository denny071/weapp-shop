<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','title', 'description', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }


//    public function decreaseStock($amount)
//    {
//        if ($amount < 0) {
//            throw new InternalException('减库存不可小于0');
//        }
//
//        return $this->newQuery()->where('id', $this->id)
//            ->where('stock','>=', $amount)
//            ->decrement('stock', $amount);
//    }
//
//    public function addStock($amount)
//    {
//        if ($amount < 0) {
//            throw new InternalException('加库存不可小于0');
//        }
//        $this->increment('stock',$amount);
//
//    }

}
