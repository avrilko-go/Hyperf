<?php

declare(strict_types=1);

namespace App\Model\Cms;

use App\Model\Model;

class LinUserGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_user_group';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'user_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public $timestamps = false;
}