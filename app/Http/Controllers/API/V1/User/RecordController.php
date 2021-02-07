<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\API\V1\Controller;
use App\Models\BrowseRecord;
use App\Transformers\BrowseRecordTransformer;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * RecordController 记录
 */
class RecordController extends Controller
{
    protected $pageSize = 10;

     /**
     * 获得浏览记录
     *
     * @return \Dingo\Api\Http\Response
     */
    public function get()
    {
        $request = $this->checkRequest();

        $page = $request->input("page","1");

        $handler  = BrowseRecord::where("user_id",$this->user()->id)->where("is_deleted",false)->limit(100);

        $total = $handler->count();

        $browseRecords = $handler->orderBy("created_at","desc")
            ->offset(($page-1) * $this->pageSize)->limit($this->pageSize)->get();

        $pager = new LengthAwarePaginator($browseRecords, $total, $this->pageSize, $page);
        return $this->response->paginator($pager,BrowseRecordTransformer::class);
   }


    /**
     * 删除浏览记录
     *
     * @param integer $id
     * @return void
     */
    public function destroy(int $id)
    {
        BrowseRecord::where("id",$id)
        ->where("user_id",$this->user()->id)
        ->update(["is_deleted" => true]);
        return $this->response->noContent();
   }

}
