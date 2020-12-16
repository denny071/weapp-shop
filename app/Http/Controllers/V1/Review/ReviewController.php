<?php

namespace App\Http\Controllers\V1\Review;

use App\Http\Controllers\V1\Controller;
use App\Http\Controllers\V1\Order\OrderController;
use App\Models\OrderItem;
use App\Models\Product;
use App\Transformers\ReviewTransformer;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{

    protected $pageSize = 10;

    /**
     * 显示评价
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function showByOrder($orderNo)
    {
        $order = (new OrderController)->getOrder($orderNo);

        $reviews = OrderItem::where("order_id",$order->id)
            ->whereNotNull("review")
            ->orderBy("reviewed_at","desc")
            ->get();
        return $this->response->collection($reviews,ReviewTransformer::class);
    }

    /**
     * 显示商品评价
     *
     * @param $productId
     * @return \Dingo\Api\Http\Response|void
     */
    public function showByProduct($productId)
    {
        $request = $this->checkRequest();

        $page = $request->input("page",1);
        $product = Product::find($productId);
        if (!$product) {
            $this->errorInternal("179001");
        }
        $handler =  OrderItem::where("product_id",$productId)->whereNotNull("review");
        $reviewCount = $handler->count();
        $reviews = $handler->orderBy("reviewed_at","desc")->offset(($page-1)*$this->pageSize)->limit($this->pageSize)->get();


        $pager = new LengthAwarePaginator($reviews, $reviewCount, $this->pageSize, $page);

        return $this->response->paginator($pager,ReviewTransformer::class);
    }

    /**
     * 用户评价
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws InvalidRequestException
     */
    public function store()
    {
        $request = $this->checkRequest();

        $orderItem = OrderItem::find($request->order_item_id);

        if ($orderItem->order->user_id != $this->user()->id) {
            $this->errorInternal("179002");
        }
        // 校验权限
        if ($orderItem->order->ship_status != "received") {
            $this->errorInternal("179003");
        }
        // 判断是否已经评价
       if ($orderItem->review) {
            $this->errorInternal("179004");;
       }
        $orderItem->rating = $request->start;
        $orderItem->review = $request->content;
        $orderItem->reviewed_at = Carbon::now();
        $orderItem->save();

        $result = OrderItem::query()
            ->where('product_id', $orderItem->product_id)
            ->whereNotNull("rating")->first([
                DB::raw('count(*) as review_count'),
                DB::raw('avg(rating) as rating')
            ]);
        // 更新商品的评分和评价数
        $orderItem->product->update([
            'rating'       => $result->rating,
            'review_count' => $result->review_count,
        ]);

        return $this->response->created();
    }
}
