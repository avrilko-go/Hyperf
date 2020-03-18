<?php

declare(strict_types=1);

namespace App\Model\Cms;

use App\Exception\Cms\UserException;
use App\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

class LinGroupPermission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_group_permission';

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
    protected $fillable = ['group_id', 'permission_id'];

    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';


}