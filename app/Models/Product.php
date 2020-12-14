<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Product extends Model
{
    use HasFactory;

    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crowdfunding';
    const TYPE_SECKILL = 'seckill';

    public static $typeMap = [
        self::TYPE_NORMAL  => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品',
        self::TYPE_SECKILL => '秒杀商品',
    ];

    protected $fillable = [
        'id',
        'title',
        'long_title',
        'description',
        'cover_image',
        'album_image',
        'content_image',
        'on_sale',
        'rating',
        'sold_count',
        'review_count',
        'price',
        'type',
    ];

    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }


    // 关联类目
    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    // 与商品SKU关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }


    /**
     * 商品属性
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(ProductProperty::class);
    }




    // 关联众筹
    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class);
    }



    /**
     * 关联秒杀
     */
    public function seckill()
    {
        return $this->hasOne(SeckillProduct::class);
    }

    public function getGroupedPropertiesAttribute()
    {
        return $this->property
            // 按照属性名聚合，返回的集合的 key 是属性名，value 是包含该属性名的所有属性集合
            ->groupBy('name')
            ->map(function ($properties) {
                // 使用 map 方法将属性集合变为属性值集合
                return $properties->pluck('value')->all();
            });
    }


    /**
     * 转化成ES对象
     *
     * @return array
     */
    public function toESArray()
    {
        // 只取出需要的字段
        $arr = Arr::only($this->toArray(), [
            'id',
            'type',
            'title',
            'catalog_id',
            'long_title',
            'on_sale',
            'rating',
            'sold_count',
            'review_count',
            'price',
        ]);


        // 如果商品有类目，则 catalog 字段为类目名数组，否则为空字符串
        $arr['catalog'] = $this->catalog ? explode(' - ', $this->catalog->full_name) : '';
        // 类目的 path 字段
        $arr['catalog_path'] = $this->catalog ? $this->catalog->path : '';
        // strip_tags 函数可以将 html 标签去除
        $arr['description'] = strip_tags($this->description);
        // 只取出需要的 SKU 字段
        $arr['skus'] = $this->skus->map(function (ProductSku $sku) {
            return Arr::only($sku->toArray(), ['title', 'description', 'price']);
        });

        $arr['properties'] = $this->properties->map(function (ProductProperty $property) {

            // 对应地增加一个 search_value 字段，用符号 : 将属性名和属性值拼接起来
            return array_merge(Arr::only($property->toArray(), ['name', 'value']), [
                'search_value' => $property->name.':'.$property->value,
            ]);
        });
        return $arr;

    }

    /**
     * 按照给入的ID 进行排序
     *
     * @param $query
     * @param $ids
     * @return mixed
     */
    public function scopeByIds($query, $ids)
    {
        return $query->whereIn('id', $ids)->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $ids)));
    }
}
