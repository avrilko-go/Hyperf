<?php

declare(strict_types=1);

namespace App\Annotation;

use App\Init\AuthInit;
use App\Model\Cms\LinPermission;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;

/**
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class Auth extends AbstractAnnotation
{
    public $auth;

    public $module;

    public $hidden = false;

    /**
     * @Inject()
     * @var LinPermission
     */
    private $permission;

    public function collectMethod(string $className, ?string $target): void
    {
        $routeName = AuthInit::makeKey($className, $target);
        AuthInit::addAuth($routeName,$this->auth, $this->module, $this->hidden);
        AnnotationCollector::collectMethod($className, $target, static::class, $this);
    }
}