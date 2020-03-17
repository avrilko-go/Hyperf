<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\Cms\ForbiddenException;
use App\Init\AuthInit;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\HttpServer\Contract\RequestInterface;


class BackendAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @Inject()
     * @var TokenService
     */
    private $token;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatch = $this->request->getAttribute(Dispatched::class);
        list($class,$method) = $dispatch->handler->callback;
        $routeName = AuthInit::makeKey($class, $method);
        $authName = AuthInit::get($routeName);
        if (empty($authName)) { // 没有设置权限代表所有用户都可以访问
            return $handler->handle($request);
        }

        // 判断用户是否为admin
        $user = $this->token->userAuth();
        if ($user['admin'] == 2) { // 超级管理员拥有一切权限
            return $handler->handle($request);
        }

        $authList = $this->recursiveForeach($user['auths']);
        // 判断接口权限是否在账户拥有权限数组内
        $allowable = in_array($authName, $authList) ? true : false;
        if (in_array($authName, $authList)) {
            return $handler->handle($request);
        }

        throw new ForbiddenException();
    }

    /**
     * 递归遍历用户权限字段的数组
     * @param $array
     *
     * @return array
     */
    protected function recursiveForeach($array) :array
    {
        static $authList = [];
        if (!is_array($array)) {
            return $authList;
        }
        foreach ($array as $key => $val) {
            if (is_array($val) && !isset($val['auth'])) {
                $this->recursiveForeach($val);
            } else {
                array_push($authList, $val['auth']);
            }
        }
        return $authList;
    }
}