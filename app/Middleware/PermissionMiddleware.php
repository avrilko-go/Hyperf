<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Init\AuthInit;
use App\Model\Cms\LinPermission;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!AuthInit::idReady()) { // 没准备好
            AuthInit::initData();
        }
        return $handler->handle($request);
    }
}