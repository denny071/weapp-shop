<?php

namespace App\Exceptions;

use Exception;
use \Dingo\Api\Routing\Helpers;

class InvalidRequestException extends Exception
{
    use Helpers;

    /**
     * __construct 初始化
     *
     * @param  mixed $message   信息
     * @param  mixed $code      编码
     * @return void
     */
    public function __construct(string $message = "", int $code = 400)
    {
        parent::__construct($message, $code);
    }

    /**
     * render 渲染
     *
     * @return void
     */
    public function render()
    {
        return $this->response->errorBadRequest($this->message);
    }
}
