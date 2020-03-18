<?php

declare(strict_types=1);

namespace App\Exception\Cms;

use App\Exception\BaseException;

class GroupException extends BaseException
{
    public $code = 404;

    public $msg = "用户组不存在";

    public $errorCode = 10070;
}