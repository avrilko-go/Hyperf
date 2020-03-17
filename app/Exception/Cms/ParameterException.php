<?php

declare(strict_types=1);

namespace App\Exception\Cms;

use App\Exception\BaseException;

class ParameterException extends BaseException
{
    public $code = 400;
    public $msg  = '参数错误';
    public $errorCode = 10030;
}