<?php

declare(strict_types=1);

namespace App\Annotation;

use App\Init\AuthInit;
use App\Init\LogInit;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationCollector;

/**
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class Log extends AbstractAnnotation
{
    public $message;

    public function collectMethod(string $className, ?string $target): void
    {
        $key = AuthInit::makeKey($className, $target);
        LogInit::addLog($key, $this->message);
        AnnotationCollector::collectMethod($className, $target, static::class, $this);
    }
}