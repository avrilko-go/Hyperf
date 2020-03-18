<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\UserLog;
use App\Exception\Cms\LogException;
use App\Model\Cms\LinLog;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @Listener()
 */
class UserLogListener implements ListenerInterface
{
    /**
     * @Inject()
     * @var LinLog
     */
    private $log;

    /**
     * @Inject()
     * @var TokenService
     */
    private $token;

    /**
     * @Inject()
     * @var RequestInterface
     */
    private $request;

    /**
     * @Inject()
     * @var ResponseInterface
     */
    private $response;

    public function listen(): array
    {
        // 返回一个该监听器要监听的事件数组，可以同时监听多个事件
        return [
            UserLog::class,
        ];
    }

    /**
     * @param UserLog $event
     */
    public function process(object $event)
    {
        $data = $event->data;

        // 行为逻辑
        if (empty($data)) {
            throw new LogException([
                'msg' => '日志信息不能为空'
            ]);
        }

        if (is_array($data)) {
            list('uid' => $uid, 'username' => $username, 'msg' => $message, 'code' => $code) = $data;
        } else {
            $uid = $this->token->getCurrentUID();
            $username = $this->token->getCurrentName();
            $message = $data;
        }

        var_dump($data);
        $insertData = [
            'message' => $username . $message,
            'user_id' => $uid,
            'username' => $username,
            'status_code' => $code,
            'method' => $this->request->getMethod(),
            'path' => $this->request->getPathInfo(),
        ];

        $this->log->create($insertData);
    }
}