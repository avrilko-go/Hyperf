<?php

declare(strict_types=1);

namespace App\Exception\Cms;

use App\Exception\BaseException;

class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg  = '权限不足，请联系管理员';
    public $errorCode = 10002;
}
