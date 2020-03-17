<?php

declare(strict_types=1);

namespace App\Util;


class Util
{
    public static function splitModules(array $auths, string $key = 'module')
    {
        if (empty($auths)) {
            return [];
        }

        $items = [];
        $result = [];

        foreach ($auths as $key => $value) {
            if (isset($items[$value['module']])) {
                $items[$value['module']][] = $value;
            } else {
                $items[$value['module']] = [$value];
            }
        }
        foreach ($items as $key => $value) {
            $item = [
                $key => $value
            ];
            array_push($result, $item);
        }
        return $result;
    }
}


