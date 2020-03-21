<?php
declare(strict_types=1);

namespace App\Init;


class LogInit
{
    /**
     * @var array 日志初始化map key为类名称@方法名  value消息名称
     */
    private static $LogMap = [];

    public static function addLog(string $routeName, string $message) :void
    {
        self::$LogMap[$routeName] = $message;
    }

    /**
     * 获取日志列表
     *
     * @return array
     */
    public static function getLog() :array
    {
        return self::$LogMap;
    }

    /**
     *  获取日志内容
     *
     * @param string $key
     *
     * @return string
     */
    public static function get(string $key) :string
    {
        if (isset(self::$LogMap[$key])) {
            return self::$LogMap[$key];
        } else {
            return "";
        }
    }

}
