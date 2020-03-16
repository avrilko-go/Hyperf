<?php

declare(strict_types=1);

namespace App\Exception\Cms;

use App\Exception\BaseException;

class UserException extends BaseException
{
    public $code = 404;

    public $message = "账户不存在";

    public $errorCode = 10020;
}