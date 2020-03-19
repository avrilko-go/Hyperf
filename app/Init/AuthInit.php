<?php
declare(strict_types=1);

namespace App\Init;

use App\Model\Cms\LinPermission;

class AuthInit
{
    private static $init = false;

    /**
     * @var array 权限初始化map key为类名称@方法名  value为权限
     */
    private static $authMap = [];

    public static function addAuth(string $routeName, string $authName, string $moduleName, bool $hidden, bool $login) :void
    {
        self::$authMap[$routeName] = [
            'authName' => $authName,
            'moduleName' => $moduleName,
            'hidden' => $hidden,
            'login' => $login,
            'id' => 0,
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

    /**
     *  获取权限内容
     *
     * @param string $key
     *
     * @return array
     */
    public static function get(string $key) :array
    {
        if (isset(self::$authMap[$key])) {
            return self::$authMap[$key];
        } else {
            return [];
        }
    }

    /**
     * 按规则生成key
     *
     * @param string $class
     * @param string $method
     *
     * @return string
     */
    public static function makeKey(string $class, string $method):string
    {
        $routeName = array_slice(explode('\\',$class),2); // 截取掉前面App\Controller提高性能
        array_push($routeName, $method);
        return implode("@", $routeName);
    }

    /**
     * 获取前端显示的权限列表
     *
     * @return array
     */
    public static function geAuthList() :array
    {
        $map = self::getAuth();
        $authList = [];
        foreach ($map as $key => $value) {
            $authName = $value['authName'];
            $moduleName = $value['moduleName'];
            $hidden = $value['hidden'];
            if (!$hidden) { // 隐藏的不显示在权限列表中
                if (!isset($authList[$moduleName])) {
                    $authList[$moduleName] = [];
                }
                $authList[$moduleName][] = [
                    'name' => $authName,
                    'module' => $moduleName,
                    'id' => (int)$value['id']
                ];
            }
        }
        return $authList;
    }

    /**
     * 初始化权限数据
     */
    public static function initData()
    {
        $map = self::getAuth();
        foreach ($map as $key => $item) {
            if (empty($item['hidden'])) {
                $id = LinPermission::addData($item);
                $item['id'] = $id;
                $map[$key] = $item;
            }
        }

        self::$authMap = $map;
        self::$init = true;
    }

    /**
     * 是否已经准备好数据
     *
     * @return bool
     */
    public static function idReady() :bool
    {
        return self::$init === true;
    }
}
