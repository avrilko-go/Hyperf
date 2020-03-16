<?php

declare(strict_types=1);

namespace App\Event;

class UserReg
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
