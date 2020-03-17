<?php

declare(strict_types=1);

namespace App\Exception\Cms;

use App\Exception\BaseException;

class LogException extends BaseException
{
    public $code = 400;
    public $msg  = '日志信息不能为空';
    public $errorCode = 40001;
}