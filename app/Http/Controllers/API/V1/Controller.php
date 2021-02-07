<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Models\User;
use \Dingo\Api\Routing\Helpers;
use Denny071\LaravelApidoc\Helper as Apidoc;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller 基础控制器
 */
class Controller extends BaseController
{
    use Helpers;


    /**
     * checkRequest 查看返回值
     *
     * @return Request
     */
    protected function checkRequest()
    {
        $debug = debug_backtrace();
        $class = explode("\\", $debug[1]['class']);

        $request = Apidoc::checkRequest($class[count($class)-2], $debug[1]['function'], function($code,$message){
            throw new InvalidRequestException($code."|".$message);
        });
        return $request;
    }


    /**
     * user 获得用户
     *
     * @return User
     */
    protected function user() : User
    {
        // 读取配置文件
        $request = Apidoc::checkRequestByConfigFile("user", function($code,$message){
            throw new InvalidRequestException($code."|".$message);
        });
        if ($user = User::where("weapp_openid",$request->openid)->first()){
            return $user;
        } else {
            $message = Apidoc::getMessageByConfigFile("user","100502");

            throw new InternalException("100502|".$message);
        }
    }


    /**
     * errorInternal
     *
     * @param  string $code   编码
     * @param  string $append 附加信息
     * @return Exception
     */
    protected function errorInternal(string $code,$append = "") : \Exception
    {

        $debug = debug_backtrace();
        $class = explode("\\", $debug[1]['class']);

        throw throwErrorMessage($class[count($class)-2],$code,$append);
    }


}
