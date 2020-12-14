<?php

namespace App\Console\Commands\Elasticsearch;

use App\Models\Product;
use Illuminate\Console\Command;

/**
 * 将商品数据同步到 Elasticsearch 命令
 *
 * Class SyncProducts
 * @package App\Console\Commands\Elasticsearch
 */
class SyncProducts extends Command
{
    /**
     * @var string 命令
     */
    protected $signature = 'es:sync-products {--index=products}';

    /**
     * @var string 秒杀
     */
    protected $description = '将商品数据同步到 Elasticsearch';

    /**
     * 初始化
     *
     * SyncProducts constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 句柄
     */
    public function handle()
    {
        // 获取 Elasticsearch 对象
        $es = app('es');

        Product::query()
            // 预加载 SKU 和 商品属性数据，避免 N + 1 问题
            ->with(['skus', 'properties'])
            // 使用 chunkById 避免一次性加载过多数据
            ->chunkById(100, function ($products) use ($es) {
                // 输出同步信息
                $this->info(sprintf('正在同步 ID 范围为 %s 至 %s 的商品', $products->first()->id, $products->last()->id));
                // 初始化请求体
                $req = ['body' => []];
                // 遍历商品
                foreach ($products as $product) {
                    // 将商品模型转为 Elasticsearch 所用的数组
                    $data = $product->toESArray();

                    $req['body'][] = [
                        'index' => [
                            '_index' => 'products',
                            '_type'  => '_doc',
                            '_id'    => $data['id'],
                        ],
                    ];
                    $req['body'][] = $data;
                }
                try {
                    // 使用 bulk 方法批量创建
                    $es->bulk($req);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            });
        $this->info('同步完成');
    }
}
