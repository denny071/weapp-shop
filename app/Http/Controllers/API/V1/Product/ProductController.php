<?php

namespace App\Http\Controllers\API\V1\Product;

use App\Http\Controllers\API\V1\Controller;
use App\Models\BrowseRecord;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\SearchBuilders\ProductSearchBuilder;
use App\Services\ProductService;
use App\Transformers\ProductDetailTransformer;
use App\Transformers\ProductTransformer;
use App\Transformers\ReviewTransformer;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    protected $pageSize = 5;

    /**
     * 商品首页
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $request = $this->checkRequest();
        // 新建查询构造器对象，设置只搜索上架商品，设置分页
        $builder = (new ProductSearchBuilder())->onSale()->paginate($this->pageSize, $request->input('page',1));;
        if ($request->input('catalog_id') && $catalog = Catalog::find($request->input('catalog_id'))) {
            // 调用查询构造器的类目筛选
            $builder->catalog($catalog);
        }
        if ($search = $request->input('search', '')) {
            $keywords = array_filter(explode(' ', $search));
            // 调用查询构造器的关键词筛选
            $builder->keywords($keywords);
        }
        if ($search || isset($category)) {
            // 调用查询构造器的分面搜索
            $builder->aggregateProperties();
        }

        $propertyFilters = [];
        if ($filterString = $request->input('filters')) {
            $filterArray = explode('|', $filterString);
            $searchPropertyFilters = [];
            foreach ($filterArray as $filter) {
                list($name, $value) = explode(':', $filter);
                $propertyFilters[$name] = $value;
                $searchPropertyFilters[$name][] = $filter;
            }
            // 调用查询构造器的属性筛选
            $builder->propertyBatchFilter($searchPropertyFilters);
        }

        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 调用查询构造器的排序
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }
        // 最后通过 getParams() 方法取回构造好的查询参数
        $result = app('es')->search($builder->getParams());

        // 通过 collect 函数将返回结果转为集合，并通过集合的 pluck 方法取到返回的商品 ID 数组
        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        // 通过 whereIn 方法从数据库中读取商品数据
        $products = Product::query()->byIds($productIds)->get();
        // 返回一个 LengthAwarePaginator 对象
        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $this->pageSize, $request->input('page',1));


        $properties = [];
        // 如果返回结果里有 aggregations 字段，说明做了分面搜索
        if (isset($result['aggregations'])) {
            // 使用 collect 函数将返回值转为集合
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                ->map(function ($bucket) {
                    // 通过 map 方法取出我们需要的字段
                    return [
                        'key' => $bucket['key'],
                        'values' => collect($bucket['value']['buckets'])->pluck('key')->all(),
                    ];
                })->toArray();
            $properties = array_values($properties);
        }

        return $this->response->paginator($pager, ProductTransformer::class)
            ->addMeta("filters", [
                'search' => $search,
                'order' => $order,
            ])->addMeta("catalog", $catalog ?? null)
            ->addMeta("properties", $properties)
            ->addMeta("propertyFilters", $propertyFilters);
    }


    /**
     * 商品详情
     *
     * @param int $productId 商品ID
     * @param Request $request
     * @param ProductService $service
     * @return \Dingo\Api\Http\Response
     * @throws \Throwable
     */
    public function get(int $productId, ProductService $service)
    {
        $request = $this->checkRequest();
        $product = Product::find($productId);

        if(!$product->on_sale) {
            $this->errorInternal("169001");
        }
        $favored = false;
        if ($request->input("openid")) {
            $user = $this->user();
            $favored = boolval($user->favoriteProducts()->find($productId));
            $browseRecord = BrowseRecord::firstOrCreate(["user_id" => $user->id, 'type' => 'product', 'sub_id' => $productId]);
            $browseRecord->increment("times");
        }


        $similarProductIds = $service->getSimilarProductIds($product, 4);
        $similarProducts = Product::query()->byIds($similarProductIds)->get();

        $reviews = [];
        $handler =  OrderItem::with(['order.user', 'productSku'])
            ->where("product_id",$productId)
            ->whereNotNull("review")->limit(2);

        $dataList = $handler->orderBy("reviewed_at","desc")->get();
        if ($dataList) {
            foreach ($dataList as $orderItem) {
                $reviews[] = (new ReviewTransformer())->transform($orderItem);
            }
        }
        return $this->response->item($product, ProductDetailTransformer::class)
            ->addMeta("favored", $favored)
            ->addMeta("reviews", $reviews)
            ->addMeta("review_count", $handler->count())
            ->addMeta("similar", $similarProducts);
    }
}
