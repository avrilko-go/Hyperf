<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Exception\BaseException;
use Hyperf\Config\Annotation\Value;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @Inject()
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @Value("mode.env")
     */
    protected $mode;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof BaseException) { // 自定义的异常错误
            $data = [
                'error_code' => $throwable->errorCode,
                'message' => $throwable->message
            ];
            $data = json_encode($data, JSON_UNESCAPED_UNICODE); // 不转义中文
            return $response->withStatus($throwable->code)->withHeader("Content-Type","application/json")->withBody(new SwooleStream($data));
        } elseif ($throwable instanceof ValidationException) { // 验证器的报错
            $message = "参数验证不通过 ===> ";
            foreach ($throwable->errors() as $error) {
                foreach ($error as $e) {
                    $message.= "{$e} ";
                }
            }

            $data = [
                'error_code' => 20000,
                'message' => $message
            ];
            $data = json_encode($data, JSON_UNESCAPED_UNICODE); // 不转义中文
            return $response->withStatus(400)->withHeader("Content-Type","application/json")->withBody(new SwooleStream($data));

        } else { // 未知的错误
            if ($this->mode !== "production") { // 不是线上的生产环境
                $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
                $this->logger->error($throwable->getTraceAsString());

            } else { // 线上的环境
               // todo 将错误以邮件的形式或者钉钉报警发送给开发人员

            }

            return $response->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
        }
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
