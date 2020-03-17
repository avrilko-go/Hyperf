<?php

declare(strict_types=1);

namespace App\Controller\Cms;

use App\Annotation\Auth;
use App\Controller\AbstractController;
use App\Model\Cms\LinLog;
use App\Model\Cms\LinUser;
use App\Request\Cms\UserRequest;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @Controller(prefix="/cms/log")
 */
class LogController extends AbstractController
{
    /**
     * @Inject()
     * @var LinLog
     */
    private $log;

    /**
     * @Middlewares({
     *     @Middleware(App\Middleware\BackendAuthMiddleware::class)
     * })
     * @GetMapping(path="users")
     * @Auth(module="日志",auth="查询日志记录的用户")
     */
    public function getUsers()
    {
        return $this->log->query()->pluck('user_name')->unique();
    }

    /**
     * @Auth(module="日志",auth="查询所有日志")
     * @GetMapping(path="")
     */
    public function getLogs()
    {
        return $this->log->getLogs($this->request->query());
    }

    /**
     * @Auth(module="日志",auth="搜索日志")
     * @GetMapping(path="search")
     */
    public function getUserLogs()
    {
        return $this->log->getLogs($this->request->query());
    }

}
