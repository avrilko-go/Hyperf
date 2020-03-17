<?php

declare(strict_types=1);

namespace App\Controller\Cms;

use App\Annotation\Auth;
use App\Controller\AbstractController;
use App\Init\AuthInit;
use App\Model\Cms\LinGroup;
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
 * @Controller(prefix="/cms/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Inject()
     * @var LinGroup
     */
    private $group;

    /**
     * @GetMapping(path="group/all")
     */
    public function getGroupAll()
    {
        return $this->group->all();
    }

    /**
     * @GetMapping(path="authority")
     */
    public function authority()
    {
        return AuthInit::geAuthList();
    }
}
