<?php

declare(strict_types=1);

namespace App\Controller\Cms;

use App\Controller\AbstractController;
use App\Event\UserLog;
use App\Model\Cms\LinUser;
use App\Request\Cms\UserRequest;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @Controller(prefix="/cms/user")
 */
class UserController extends AbstractController
{
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
     * @PostMapping(path="login")
     */
    public function login(UserRequest $request)
    {
        $request->validated();
        $params = $request->post();
        $user = $this->user->verify($params['username'], $params['password']);
        $this->eventDispatcher->dispatch(new UserLog(['uid' => $user->id, 'username' => $user->username, 'msg' => '登陆成功获取了令牌','code' => 200]));
        return $this->token->getToken($user);
    }

    /**
     * @GetMapping(path="auths")
     */
    public function getAllowedApis()
    {
        $uid = $this->token->getCurrentUID();
        return $this->user->getUserByUID($uid);
    }



}
