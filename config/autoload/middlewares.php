<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

use App\Middleware\CorsMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;

return [
    'http' => [
        PermissionMiddleware::class, // 这个一定要放在最外层，包含权限初始化
        ValidationMiddleware::class,
        CorsMiddleware::class
    ],
];
