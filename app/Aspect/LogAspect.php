<?php
namespace App\Aspect;

use App\Annotation\Log;
use App\Exception\Cms\TokenException;
use App\Init\AuthInit;
use App\Init\LogInit;
use App\Model\Cms\LinLog;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpMessage\Base\Response;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Router\Dispatched;

/**
 * @Aspect
 */
class LogAspect extends AbstractAspect
{
    /**
     * @Inject()
     * @var Response
     */
    private $response;

    /**
     * @Inject()
     * @var RequestInterface
     */
    private $request;

    /**
     * @Inject()
     * @var TokenService
     */
    private $token;

    /**
     * @Inject()
     * @var LinLog
     */
    private $log;

    // 要切入的类，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public $classes = [
    ];

    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public $annotations = [
        Log::class
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $result = $proceedingJoinPoint->process();
        $dispatch = $this->request->getAttribute(Dispatched::class);
        list($class,$method) = $dispatch->handler->callback;
        $routeName = AuthInit::makeKey($class, $method);
        $message = LogInit::get($routeName);
        if (!empty($message)) {
            // 在调用后进行某些处理
            $statusCode = $this->response->getStatusCode();
            if (isset($result['access_token'])) {
                $accessToken = 'Bearer '.$result['access_token'];
                $uid = $this->token->getCurrentTokenVarByAccessToken('id', $accessToken);
                $username = $this->token->getCurrentTokenVarByAccessToken('username', $accessToken);
            } else {
                $uid = $this->token->getCurrentUID();
                $username = $this->token->getCurrentName();
            }
            $insertData = [
                'message' => $username . " ".$message,
                'user_id' => $uid,
                'username' => $username,
                'status_code' => $statusCode,
                'method' => $this->request->getMethod(),
                'path' => $this->request->getPathInfo(),
                'params' => json_encode($this->request->all())
            ];

            $this->log->create($insertData);
        }
        return $result;
    }
}