<?php
declare(strict_types=1);

namespace App\Init;

class AuthInit
{
    /**
     * @var array 权限初始化map key为类名称@方法名  value为权限
     */
    private static $authMap = [];


    public static function addAuth(string $routeName, string $authName, string $moduleName) :void
    {
        self::$authMap[$routeName] = [
            'authName' => $authName,
            'moduleName' => $moduleName
        ];
    }

    /**
     * 获取权限列表
     *
     * @return array
     */
    public static function getAuth() :array
    {
        return self::$authMap;
    }
}
