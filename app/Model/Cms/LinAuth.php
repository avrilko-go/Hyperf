<?php

declare(strict_types=1);

namespace App\Model\Cms;


use App\Model\Model;

class LinAuth extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_auth';

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
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * 获取当前权限组下面的所有权限
     *
     * @param int $id
     *
     * @return array
     */
    public function getAuthByGroupID(int $id)
    {
        return $this->query()->where('group_id', $id)->get()->toArray();
    }

}