<?php

declare(strict_types=1);

namespace App\Controller\Cms;

use App\Annotation\Auth;
use App\Controller\AbstractController;
use App\Init\AuthInit;
use App\Model\Cms\LinGroup;
use App\Model\Cms\LinGroupPermission;
use App\Model\Cms\LinLog;
use App\Model\Cms\LinUser;
use App\Model\Cms\LinUserGroup;
use App\Model\Cms\LinUserIdentity;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;

/**
 * @Middlewares({
 *  @Middleware(App\Middleware\BackendAuthMiddleware::class)
 * })
 *
 * @Controller(prefix="/cms/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Inject()
     * @var LinGroup
     */
    private $group;

    /**
     * @Inject()
     * @var LinGroupPermission
     */
    private $groupPermission;

    /**
     * @Inject()
     * @var LinUserGroup
     */
    private $userGroup;

    /**
     * @Inject()
     * @var LinUser
     */
    private $user;

    /**
     * @Inject()
     * @var LinLog
     */
    private $log;

    /**
     * @Inject()
     * @var LinUserIdentity
     */
    private $userIdentity;

    /**
     * @Auth(auth="获取所有分组信息",module="管理员",login=true,hidden=true)
     * @GetMapping(path="group/all")
     */
    public function getGroupAll()
    {
        return $this->group->all();
    }

    /**
     * @Auth(auth="获取所有权限内容",module="管理员",login=true,hidden=true)
     * @GetMapping(path="permission")
     */
    public function authority()
    {
        return AuthInit::geAuthList();
    }

    /**
     * @Auth(auth="获取单个分组信息",module="管理员",login=true,hidden=true)
     * @GetMapping(path="group/{id}")
     */
    public function groupInfo(int $id)
    {
        return $this->group->groupInfo($id);
    }

    /**
     * @Auth(auth="添加组的权限",module="管理员",login=true,hidden=true)
     * @PostMapping(path="permission/dispatch/batch")
     */
    public function batchPermission()
    {
        $groupId = $this->request->post('group_id');
        $permissionIds = $this->request->post('permission_ids');
        foreach ($permissionIds as $permissionId) {
            $insertData = [
                'group_id' => $groupId,
                'permission_id' => $permissionId
            ];
            $this->groupPermission->create($insertData);
        }

        return [
            'code' => 7,
            'message' => '添加权限成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }

    /**
     * @Auth(auth="移除组的权限",module="管理员",login=true,hidden=true)
     * @PostMapping(path="permission/remove")
     */
    public function removePermission()
    {
        $groupId = $this->request->post('group_id');
        $permissionIds = $this->request->post('permission_ids');
        $this->groupPermission->query()->where('group_id', $groupId)->whereIn('permission_id', $permissionIds)->delete();

        return [
            'code' => 8,
            'message' => '删除权限成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }

    /**
     * @Auth(auth="添加分组信息",module="管理员",login=true,hidden=true)
     * @PostMapping(path="group")
     */
    public function addGroup()
    {
        $name = $this->request->post('name');
        $info = $this->request->post('info');
        $permissionIds = $this->request->post('permission_ids');
        $this->group->addGroup($name, $info, $permissionIds);

        return [
            'code' => 13,
            'message' => '新建分组成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }

    /**
     * @Auth(auth="删除分组信息",module="管理员",login=true,hidden=true)
     * @DeleteMapping(path="group/{id}")
     */
    public function deleteGroup(int $id)
    {
        $this->groupPermission->query()->where('group_id', $id)->delete();
        $this->group->query()->where('id', $id)->delete();
        $this->userGroup->query()->where('group_id', $id)->delete();

        return [
            'code' => 6,
            'message' => '删除分组成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }

    /**
     * @GetMapping(path="users")
     */
    public function users()
    {
        return $this->user->getUserList($this->request->all());
    }

    /**
     * @Auth(auth="修改用户信息",module="管理员",login=true,hidden=true)
     * @PutMapping(path="user/{id}")
     */
    public function storeUser(int $id)
    {
        $this->user->storeUser($id,$this->request->all());

        return [
            'code' => 9,
            'message' => '修改信息成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }

    /**
     * @Auth(auth="删除用户信息",module="管理员",login=true,hidden=true)
     * @DeleteMapping(path="user/{id}")
     */
    public function deleteUser(int $id)
    {
        $this->userGroup->query()->where('user_id',$id)->delete();
        $this->log->query()->where('user_id',$id)->delete();
        $this->userIdentity->query()->where('user_id',$id)->delete();
        $this->user->query()->where('id',$id)->delete();

        return [
            'code' => 6,
            'message' => '移除用户成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }

    /**
     * @Auth(auth="修改用户密码",module="管理员",login=true,hidden=true)
     * @PutMapping(path="user/{id}/password")
     */
    public function password()
    {
        $password = $this->request->input('new_password');
        $this->userIdentity->changePassword($password);

        return [
            'code' => 9,
            'message' => '修改登陆密码成功',
            'request' => $this->request->getMethod(). " ".$this->request->getPathInfo()
        ];
    }
}
