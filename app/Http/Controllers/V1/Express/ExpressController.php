<?php

namespace App\Http\Controllers\V1\Express;

use App\Http\Controllers\V1\Controller;
use App\Models\ExpressCompany;
use App\Models\Order;

/**
 * ExpressController 快递
 */
class ExpressController extends Controller
{

    /**
     * 访问快递地址
     *
     * @var string
     */
    private $apiHost = "https://wuliu.market.alicloudapi.com";

    /**
     * index 获得快递信息
     *
     * @param  mixed $orderNo
     * @return void
     */
    public function index()
    {
        $request = $this->checkRequest();

        $order = Order::where("no",$request->order_no)->first();
        if (!$order) {
            $this->errorInternal("149001");
        }
        if ($order->ship_status == Order::SHIP_STATUS_PENDING) {
            $this->errorInternal("149002");
        }
        if (!$order->ship_data) {
            $this->errorInternal("149003");
        }
        if ($order->ship_status == Order::SHIP_STATUS_RECEIVED) {
            return $this->response->array(json_decode($order->ship_info,true));
        }

        $data =  $this->getExpressFlow($order->ship_data['express_no']);
        if ($data['status'] == 0) {
            if($data['result']['deliverystatus'] == 3){
                $order->ship_status = Order::SHIP_STATUS_RECEIVED;
            }
            $order->ship_info = json_encode($data,JSON_UNESCAPED_UNICODE);
            $order->save();
            return $this->response->array($data);
        } else {
            $this->errorInternal("149005",$data['msg']);
        }

    }

    /**
     * 获得快递物流信息
     *
     * @param string $expressNo
     * @return mixed|void
     */
    private function getExpressFlow(string $expressNo)
    {
        $appcode = env("EXPRESS_CODE","");//替换成自己的阿里云appcode
        if (!$appcode) {
            $this->errorInternal("149004");
        }
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "no=".$expressNo;  //参数写在这里
        $url = $this->apiHost . "/kdi" . "?" . $querys;//url拼接

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        //curl_setopt($curl, CURLOPT_HEADER, true); 如不输出json, 请打开这行代码，打印调试头部状态码。
        //状态码: 200 正常；400 URL无效；401 appCode错误； 403 次数用完； 500 API网管错误
        if (1 == strpos("$".$this->apiHost , "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        return json_decode(curl_exec($curl),true);
    }

}
