<?php
namespace App\Console\Commands\Elasticsearch\Indices;

use Illuminate\Support\Facades\Artisan;

class ProjectIndex
{
    public static function getAliasName()
    {
        return 'products';
    }

    public static function getProperties()
    {
        return [
            // 类型
            'type'          => ['type' => 'keyword'],
            // 标题
            'title'         => ['type' => 'text', 'analyzer' => 'ik_smart', 'search_analyzer' => 'ik_smart_synonym'],
            // 长标题
            'long_title'    => ['type' => 'text', 'analyzer' => 'ik_smart', 'search_analyzer' => 'ik_smart_synonym'],
            // 分类
            'catalog_id'   => ['type' => 'integer'],
            // 分类标题
            'catalog'      => ['type' => 'keyword'],
            // 分类路径
            'catalog_path' => ['type' => 'keyword'],
            // 秒杀
            'description'   => ['type' => 'text', 'analyzer' => 'ik_smart'],
            // 价格
            'price'         => ['type' => 'scaled_float', 'scaling_factor' => 100],
            // 是否在售
            'on_sale'       => ['type' => 'boolean'],
            // 浮点
            'rating'        => ['type' => 'float'],
            // 销售数量
            'sold_count'    => ['type' => 'integer'],
            // 回复数量
            'review_count'  => ['type' => 'integer'],
            // 子产品
            'skus'          => [
                // 嵌套类型
                'type'       => 'nested',
                // 属性
                'properties' => [
                    // 标题
                    'title'       => [
                        'type'            => 'text',
                        'analyzer'        => 'ik_smart',
                        'search_analyzer' => 'ik_smart_synonym',
                    ],
                    // 描述
                    'description' => ['type' => 'text', 'analyzer' => 'ik_smart'],
                    // 价格
                    'price'       => ['type' => 'scaled_float', 'scaling_factor' => 100],
                ],
            ],
            // 属性
            'properties'    => [
                // 嵌套类型
                'type'       => 'nested',
                // 属性
                'properties' => [
                    // 标题
                    'name'         => ['type' => 'keyword'],
                    // 值
                    'value'        => ['type' => 'keyword'],
                    // 查询值
                    'search_value' => ['type' => 'keyword'],
                ],
            ],
        ];
    }

    /**
     * 分词查询设置
     *
     * @return array
     */
    public static function getSettings()
    {
        return [
            'analysis' => [
                'analyzer' => [
                    'ik_smart_synonym' => [
                        // 自定义
                        'type'      => 'custom',
                        // 分词器
                        'tokenizer' => 'ik_smart',
                        // 过滤器
                        'filter'    => ['synonym_filter'],
                    ],
                ],
                'filter'   => [
                    // 同义词过滤器
                    'synonym_filter' => [
                        // 类型
                        'type'          => 'synonym',
                        // 同义词定义文件
                        'synonyms_path' => 'analysis/synonyms.txt',
                    ],
                ],
            ],
        ];
    }

    /**
     * 创建新索引
     *
     * @param $indexName
     */
    public static function rebuild($indexName)
    {
        // 通过 Artisan 类的 call 方法可以直接调用命令
        // call 方法的第二个参数可以用数组的方式给命令传递参数
        Artisan::call('es:sync-products', ['--index' => $indexName]);
    }
}
