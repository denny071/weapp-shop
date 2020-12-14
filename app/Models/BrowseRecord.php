<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrowseRecord extends Model
{
    protected $fillable = ['user_id','type','sub_id','times'];



    public function getData()
    {
        if ($this->type == "product"){
            $product = Product::where("id",$this->sub_id)->first();
            return [
              "product_id" => $product->id,
              "title" => $product->title,
              "image" => $product->image,
              "price" => $product->price,
            ];
        };
    }

}
