<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class BaseException extends \Exception
{
    // 错误码
    public $code = 400;

    // 错误信息
    public $message = "参数错误";

    // 错误码
    public $errorCode = 10000;

    public function __construct(array $params = [])
    {
        if (isset($params['code'])) {
            $this->code = (int)$params['code'];
        }

        if (isset($params['message'])) {
            $this->message = (string)$params['message'];
        }

        if (isset($params['errorCode'])) {
            $this->errorCode = (int)$params['errorCode'];
        }

        parent::__construct();
    }
}
