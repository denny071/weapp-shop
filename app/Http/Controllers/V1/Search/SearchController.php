<?php

namespace App\Http\Controllers\V1\Search;

use App\Http\Controllers\V1\Controller;
use App\Models\SearchWord;
use App\Transformers\SearchWordTransformer;

class SearchController extends Controller
{
     /**
     * 查询词列表
     */
    public function index(){

        $words = SearchWord::where("user_id", $this->user()->id)
            ->where("is_deleted",false)
            ->orderBy("updated_at","desc")
            ->limit(env("SEARCH_WORD_HISTORY_LIMIT",10))
            ->get();

        return $this->response->collection($words,SearchWordTransformer::class);
    }

    /**
     * 添加查询词
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store(){

        $request = $this->checkRequest();

        $searchWord =  SearchWord::firstOrCreate([
            "user_id" => $this->user()->id,
            "word" => $request->word
        ]);

        $searchWord->increment("times");

        return $this->response->created();
    }

    /**
     * 清空历史记录
     */
    public function clear()
    {
        SearchWord::where("user_id", $this->user()->id)->update(["is_deleted" => true]);

        return $this->response->noContent();
    }

}
