<?php

declare(strict_types=1);

namespace App\Model\Cms;


use App\Exception\Cms\GroupException;
use App\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;

class LinGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_group';

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
    protected $fillable = ['name', 'info'];

    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    /**
     * @Inject()
     * @var LinGroupPermission
     */
    private $groupPermission;

    /**
     * @Inject()
     * @var LinPermission
     */
    private $permission;

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function groupInfo(int $id) :array
    {
        try {
            $group = $this->query()->findOrFail($id)->toArray();
        }catch (ModelNotFoundException $e) {
            throw new GroupException();
        }

        // 查询该组对应的权限
        if ($group['name'] === 'admin') { // 最高权限
            $permissions = $this->permission->query()->get()->makeHidden(['create_time', 'update_time', 'delete_time'])->toArray();
        } else {
            $permissionIds = $this->groupPermission->query()->where('group_id', $group['id'])->get()->pluck('permission_id');
            $permissions = $this->permission->query()->whereIn('id', $permissionIds)->get()->makeHidden(['create_time', 'update_time', 'delete_time'])->toArray();
        }

        $group['permissions'] = $permissions;

        return $group;
    }

    /**
     * 添加用户组
     *
     * @param string $name
     * @param string $info
     * @param array $permissionIds
     */
    public function addGroup(string $name, string $info, array $permissionIds)
    {
        $insertData = [
            'name' => $name,
            'info' => $info,
        ];
        $group = $this->create($insertData);
        foreach ($permissionIds as $permissionId) {
            $data = [
                'group_id' => $group->id,
                'permission_id' => $permissionId
            ];
            $this->groupPermission->create($data);
        }
    }

}