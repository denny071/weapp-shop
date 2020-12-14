<?php

namespace App\Exceptions;

use Dingo\Api\Routing\Helpers;
use Exception;

class InternalException extends Exception
{
    use Helpers;


    /**
     * __construct 初始化
     *
     * @param  mixed $message   信息
     * @param  mixed $code      编码
     * @return void
     */
    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return $this->response->errorBadRequest($this->message);
    }
}
