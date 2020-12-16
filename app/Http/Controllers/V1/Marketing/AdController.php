<?php

namespace App\Http\Controllers\V1\Marketing;

use App\Http\Controllers\V1\Controller;

/**
 * AdController 广告
 */
class AdController extends Controller
{
    /**
     * 小程序广告
     *
     * @return mixed
     */
    public function index()
    {
        $data =[
            [
                "picUrl" => "https://d.vpimg1.com/upcb/2019/09/04/182/ias_156756261645462_570x273_90.jpg",
                "advertUrl" => "/pages/goods_detail?id=2c9257a15f37e432015f3d10151e01e6",
            ],
            [
                "picUrl" => "https://d.vpimg1.com/upcb/2019/09/04/182/ias_156756261645462_570x273_90.jpg",
                "advertUrl" => "/pages/home_detail?code=019",
            ],
            [
                "picUrl" => "https://d.vpimg1.com/upcb/2019/09/04/182/ias_156756261645462_570x273_90.jpg",
                "advertUrl" => "/pages/home_detail?code=017",
            ],
            [
                "picUrl" => "https://d.vpimg1.com/upcb/2019/09/04/182/ias_156756261645462_570x273_90.jpg",
                "advertUrl" => "/pages/home_detail?code=017",
            ],
            [
                "picUrl" => "https://d.vpimg1.com/upcb/2019/09/04/182/ias_156756261645462_570x273_90.jpg",
                "advertUrl" => "/pages/home_detail?code=017",
            ]
        ];

        return $this->response->array($data);
    }

}
