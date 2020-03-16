<?php

declare(strict_types=1);

namespace App\Controller\Cms;

use App\Controller\AbstractController;
use App\Model\Cms\LinUser;
use App\Request\Cms\UserRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;

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
     * @PostMapping(path="login")
     */
    public function login(UserRequest $request, ResponseInterface $response)
    {
        $request->validated();
        $params = $request->post();
        var_dump($params);
        $this->user->verify($params['username'], $params['password']);
    }
}
