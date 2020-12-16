<?php

namespace App\SearchBuilders;

use App\Models\Catalog;
use App\Models\Category;

/**
 * 产品搜索控制器
 *
 * Class ProductSearchBuilder
 * @package App\SearchBuilders
 */
class ProductSearchBuilder
{

    /**
     * @var array 初始化查询
     */
    protected $params = [
        'index' => 'products',
        'type' => '_doc',
        'body' => [
            'query' => [
                'bool' => [
                    'filter' => [],
                    'must' => [],
                ],
            ],
        ],
    ];

    /**
     * 分页查询
     *
     * @param int $size 每页数量
     * @param int $page 第几页
     * @return $this
     */
    public function paginate($size, $page)
    {
        $this->params['body']['from'] = ($page - 1) * $size;
        $this->params['body']['size'] = $size;

        return $this;
    }


    /**
     * 筛选上架状态的商品
     *
     * @return $this
     */
    public function onSale()
    {
        $this->params['body']['query']['bool']['filter'][] = ['term' => ['on_sale' => true]];

        return $this;
    }


    /**
     * 按类目筛选商品
     *
     * @param Category $catalog
     */
    public function catalog(Catalog $catalog)
    {
        if ($catalog->is_directory) {
            $this->params['body']['query']['bool']['filter'][] = [
                'prefix' => ['category_path' => $catalog->path . $catalog->id . '-'],
            ];
        } else {
            $this->params['body']['query']['bool']['filter'][] = ['term' => ['catalog_id' => $catalog->id]];
        }
    }


    /**
     * 添加搜索词
     *
     * @param $keywords
     * @return $this
     */
    public function keywords($keywords)
    {
        // 如果参数不是数组则转为数组
        $keywords = is_array($keywords) ? $keywords : [$keywords];
        foreach ($keywords as $keyword) {
            $this->params['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $keyword,
                    'fields' => [
                        'title^3',
                        'long_title^2',
                        'category^2',
                        'description',
                        'skus.title',
                        'skus.description',
                        'properties.value',
                    ],
                ],
            ];
        }

        return $this;
    }


    /**
     * 分面搜索的聚合
     *
     * @return $this
     */
    public function aggregateProperties()
    {
        $this->params['body']['aggs'] = [
            'properties' => [
                'nested' => [
                    'path' => 'properties',
                ],
                'aggs' => [
                    'properties' => [
                        'terms' => [
                            'field' => 'properties.name',
                        ],
                        'aggs' => [
                            'value' => [
                                'terms' => [
                                    'field' => 'properties.value',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this;
    }

    /**
     * 添加一个按商品属性筛选的条件
     *
     * @param string $name 名称
     * @param string $value 值
     * @param string $type
     * @return $this
     */
    public function propertyFilter($name, $value, $type = 'filter')
    {
        $this->params['body']['query']['bool'][$type][] = [
            'nested' => [
                'path'  => 'properties',
                'query' => [
                    ['term' => ['properties.search_value' => $name.':'.$value]],
                ],
            ],
        ];

        return $this;
    }


    /**
     * 添加一个按商品属性筛选的条件
     *
     * @param array $searchPropertyFilters 查询条件
     * @param string $value 值
     * @param string $type
     * @return $this
     */
    public function propertyBatchFilter(array $searchPropertyFilters, $type = 'filter')
    {

        foreach ($searchPropertyFilters as $data) {
            $flag = count($data) == 1;
            $this->params['body']['query']['bool'][$type][] = [
                'nested' => [
                    'path' => 'properties',
                    'query' => [
                        [$flag ? 'term' : 'terms' => ['properties.search_value' => $flag ? $data[0] : $data]],
                    ],
                ],
            ];
        }
        return $this;
    }


    /**
     * 设置 minimum_should_match 参数
     *
     * @param string $count 数量
     * @return $this
     */
    public function minShouldMatch($count)
    {
        $this->params['body']['query']['bool']['minimum_should_match'] = (int)$count;

        return $this;
    }


    /**
     * 添加排序
     *
     * @param string $field 字段
     * @param string $direction 方向
     * @return $this
     */
    public function orderBy($field, $direction)
    {
        if (!isset($this->params['body']['sort'])) {
            $this->params['body']['sort'] = [];
        }
        $this->params['body']['sort'][] = [$field => $direction];

        return $this;
    }


    /**
     * 返回构造好的查询参数
     *
     * @return array 获得参数
     */
    public function getParams()
    {
        return $this->params;
    }
}
