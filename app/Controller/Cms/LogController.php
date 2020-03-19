<?php

declare(strict_types=1);

namespace App\Controller\Cms;

use App\Annotation\Auth;
use App\Controller\AbstractController;
use App\Model\Cms\LinLog;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;

/**
 * @Middlewares({
 *  @Middleware(App\Middleware\BackendAuthMiddleware::class)
 * })
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
     * @GetMapping(path="users")
     * @Auth(module="日志",auth="查询日志记录的用户",login=true)
     */
    public function getUsers()
    {
        return $this->log->getUsers($this->request->query());
    }

    /**
     * @Auth(module="日志",auth="查询所有日志",login=true)
     * @GetMapping(path="")
     */
    public function getLogs()
    {
        return $this->log->getLogs($this->request->query());
    }

    /**
     * @Auth(module="日志",auth="搜索日志",login=true)
     * @GetMapping(path="search")
     */
    public function getUserLogs()
    {
        return $this->log->getLogs($this->request->query());
    }

}
