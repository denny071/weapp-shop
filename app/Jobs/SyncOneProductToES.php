<?php

namespace App\Jobs;

use App\Models\Product;

/**
 * 同步商品到 ElasticSearch
 *
 * Class SyncOneProductToES
 * @package App\Jobs
 */
class SyncOneProductToES extends Job
{

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        // 添加索引
        $data = $this->product->toESArray();
        app('es')->index([
            'index' => 'products',
            'type'  => '_doc',
            'id'    => $data['id'],
            'body'  => $data,
        ]);
    }
}
