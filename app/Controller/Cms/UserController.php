<?php

declare(strict_types=1);

namespace App\Controller\Cms;

use App\Annotation\Auth;
use App\Annotation\Log;
use App\Controller\AbstractController;
use App\Model\Cms\LinUser;
use App\Model\Cms\LinUserIdentity;
use App\Request\Cms\UserRequest;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @Middlewares({
 *     @Middleware(App\Middleware\BackendAuthMiddleware::class)
 *  })
 *
 * @Controller(prefix="/cms/user")
 */
class UserController extends AbstractController
{
    /**
     * @Inject()
     * @var LinUserIdentity
     */
    private $userIdentity;

    /**
     * @Inject()
     * @var LinUser
     */
    private $user;

    /**
     * @Inject()
     * @var TokenService
     */
    private $token;

    /**
     * @Inject
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @Log(message="登陆获取了令牌")
     * @PostMapping(path="login")
     */
    public function login(UserRequest $request)
    {
        $request->validated();
        $params = $request->post();
        $user = $this->userIdentity->verify($params['username'], $params['password'], LinUserIdentity::TYPE_LOGIN_USERNAME);
        return $this->token->getToken($user);
    }

    /**
     * @Auth(auth="获取自己的权限信息",login=true,hidden=true,module="必备")
     * @GetMapping(path="permissions")
     */
    public function getAllowedApis()
    {
        $uid = $this->token->getCurrentUID();
        return $this->user->getUserInfo($uid);
    }

    /**
     * @Log(message="添加了一个用户")
     * @Auth(auth="添加一个角色",login=true,hidden=true,module="管理员")
     * @PostMapping(path="register")
     */
    public function register()
    {
        $this->user->addUser($this->request->all());
        return [
            'code' => 9,
            'message' => '注册成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }

    /**
     * @Log(message="刷新了授权令牌")
     * @Auth(auth="刷新授权",hidden=true,module="必备")
     * @GetMapping(path="refresh")
     */
    public function refresh()
    {
        return $this->token->refreshToken();
    }

}
