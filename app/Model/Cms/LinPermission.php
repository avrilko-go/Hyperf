<?php

declare(strict_types=1);

namespace App\Model\Cms;

use App\Exception\Cms\UserException;
use App\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

class LinPermission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_permission';

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
    protected $fillable = ['name', 'module'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';

    /**
     * 加入权限数据
     *
     * @param array $data
     *
     * @return int
     */
    public static function addData(array $data) :int
    {
        $insertData = [
            'name' => $data['authName'],
            'module' => $data['moduleName']
        ];

        return self::query()->firstOrCreate($insertData, $insertData)->id;
    }

    /**
     * 格式化前台数据
     *
     * @param array $permissions
     *
     * @return array
     */
    public function formatPermission(array $permissions) :array
    {
        if (empty($permissions)) {
            return  [];
        }

        $permissionArr = [];
        if (!empty($permissions)) { //
            foreach ($permissions as $key => $permission) {
                $module = $permission['module'];
                $name = $permission['name'];
                if (!isset($permissionArr[$module])) {
                    $permissionArr[$module] = [];
                }
                array_push($permissionArr[$module], ['module' => $module, 'permission' => $name]);
            }
        }

        // 格式化成lin_cms前端要求的格式
        $returnPermissions = [];
        foreach ($permissionArr as $key => $value) {
            $data[$key] = $value;
            array_push($returnPermissions, $data);
        }

        return  $returnPermissions;
    }

}