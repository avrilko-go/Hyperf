<?php

declare(strict_types=1);

namespace App\Model\Cms;

use App\Exception\Cms\TokenException;
use App\Exception\Cms\UserException;
use App\Init\AuthInit;
use App\Model\Model;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;

class LinUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_user';

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
    protected $fillable = ['username', 'nickname', 'email', 'avatar'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * @Inject()
     * @var LinGroup
     */
    private $group;

    /**
     * @Inject()
     * @var LinPermission
     */
    private $permissions;

    /**
     * @Inject()
     * @var LinUserGroup
     */
    private $userGroup;

    /**
     * @Inject()
     * @var LinGroupPermission
     */
    private $groupPermission;

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';


    /**
     * 检查密码
     *
     * @param string $rawPassword
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword(string $rawPassword, string $password) :bool
    {
        return md5($rawPassword.config('mode.app_key')) === $password;
    }

    /**
     * 获取用户信息
     *
     * @param $uid
     *
     * @return array
     * @throws UserException
     */
    public function getUserInfo(int $uid) :array
    {
        $user = $this->getUserById($uid)->setHidden(['create_time', 'update_time', 'delete_time', 'username'])->toArray();
        // 查询用户所有的权限组
        $groupIds = $this->userGroup->query()->where('user_id', $user['id'])->get()->pluck('group_id')->toArray();
        $superAdmin = $this->group->query()->whereIn('id', $groupIds)->where('name', LinGroup::ADMIN_GROUP_NAME)->first();
        $permissions = [];
        if (!empty($superAdmin)) { // 该用户拥有超级管理员的权限（直接查询所有权限返回）
            $user['admin'] = true;
            $allAuth = AuthInit::getAuth();
            foreach ($allAuth as $value) {
                $permissions[] = [
                    'module' => $value['moduleName'],
                    'name' => $value['authName']
                ];
            }
        } else { //不是则要先查询出这些分组下有多少权限id
            $user['admin'] = false;
            $permissionIds = $this->groupPermission->query()->whereIn('group_id', $groupIds)->get()->pluck('permission_id');
            if (!empty($permissionIds)) {
                $permissions = $this->permissions->query()->whereIn('id', $permissionIds)->get()->toArray();
            }
        }

        $user['permissions'] = $this->permissions->formatPermission($permissions);

        return $user;
    }

    /**
     * 获取权限列表
     *
     * @param int $uid
     *
     * @return array
     * @throws UserException
     */
    public function getUserAllPermission(int $uid) :array
    {
        $userInfo = $this->getUserInfo($uid);
        $permissions = [];
        if (!empty($userInfo['permissions'])) {
            foreach ($userInfo['permissions'] as $v) {
                foreach ($v as $key => $value) {
                    foreach ($value as $item) {
                        $permissions[] = $item['permission'];
                    }
                }
            }
        }
        return array_unique($permissions);
    }


    /**
     * 通过user_id 获取数据
     *
     * @param int $id
     *
     * @return LinUser
     * @throws UserException
     */
    public function getUserById(int $id) :LinUser
    {
        try {
            $user = $this->query()->findOrFail($id);
        } catch (\Exception $ex) {
            throw new UserException();
        }

        return $user;
    }

    /**
     * 添加用户
     *
     * @param array $params
     */
    public function addUser(array $params)
    {
        $insertData = [
            'username' => $params['username'],
            'email' => $params['email'],
        ];
        $user = $this->create($insertData);

        LinUserIdentity::create([
            'user_id' => $user->id,
            'identity_type' => LinUserIdentity::TYPE_LOGIN_USERNAME,
            'identifier' => $params['username'],
            'credential' => md5($params['password']. config('mode.app_key'))
        ]);

        if (!empty($params['group_ids'])) {
            foreach ($params['group_ids'] as $group_id) {
                $this->userGroup->create([
                    'user_id' => $user->id,
                    'group_id' => $group_id
                ]);
            }
        }
    }

    /**
     * 获取用户列表
     *
     * @param array $params
     *
     * @return array
     * @throws \App\Exception\Cms\ParameterException
     */
    public function getUserList(array $params)
    {
        $users = $this->query();
        if (isset($params['group_id']) && $params['group_id'] > 0) {
            $userIds = $this->userGroup->query()->where('group_id', $params['group_id'])->get()->pluck('user_id');
            if (!empty($userIds)) {
                $users = $users->whereIn('id', $userIds);
            }
        }

        list($start, $count) = $this->paginate();

        $totalNums = $users->count();
        $users = $users->offset($start)->limit($count)->orderByDesc('create_time')->get()->toArray();

        foreach ($users as $key => $user) { // 这里在数据库循环查数据很sb，为了适配这个数据库结构
            $user['groups'] = [];
            $groupIds = $this->userGroup->query()->where('user_id', $user['id'])->get()->pluck('group_id');
            if (!empty($groupIds)) {
                $groupInfo = $this->group->query()->whereIn('id', $groupIds)->get()->toArray();
                $user['groups'] = $groupInfo;
            }
            $users[$key] = $user;
        }

        $result = [
            'items' => $users,
            'total' => $totalNums,
            'count' => $count,
            'page' => $this->request->query('page'),
            'total_page' => ceil($totalNums / $count)
        ];
        return $result;
    }

    /**
     * 保存用户信息
     *
     * @param int $id
     * @param array $params
     *
     * @throws UserException
     */
    public function storeUser(int $id, array $params)
    {
        $email = (string)$params['email'];
        $group_ids = $params['group_ids'];
        $user = $this->getUserById($id);
        if ($user->email !== $email) {
            throw new UserException([
                "code"=> 404,
                'msg' => '邮箱不匹配',
            ]);
        }

        $this->userGroup->query()->where('user_id', $id)->delete();
        foreach ($group_ids as $group_id) {
            $insertData = [
                'user_id' => $id,
                'group_id' => $group_id
            ];
            $this->userGroup->query()->create($insertData);
        }
    }
}