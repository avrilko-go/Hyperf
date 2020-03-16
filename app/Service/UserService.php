<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Cache\Annotation\Cacheable;

class UserService
{
    /**
     * @Cacheable(prefix="user",ttl=10,listener="user-update")
     */
    public function getInfoById(int $id)
    {
        return $id . '_' . uniqid();
    }
}

