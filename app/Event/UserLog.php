<?php

declare(strict_types=1);

namespace App\Event;

class UserLog
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
