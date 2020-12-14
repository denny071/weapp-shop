<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class User extends Model
{
    use HasFactory;
    // end_at 会自动转为 Carbon 类型
    protected $dates = ['last_login_at', 'created_at'];


    protected $fillable = [
        'name',
        'avatar',
        'gender',
        'country',
        'province',
        'city',
        'language',
        'last_login_at',
        'weixin_session_key',
        'weapp_openid',
    ];



    /**
     * 用户地址
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * 序列号时间
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }



    /**
     * 收藏商品
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorites')
            ->withTimestamps()->orderBy('user_favorites.created_at','desc');
    }


}
