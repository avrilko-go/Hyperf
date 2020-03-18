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
use App\Request\Cms\UserRequest;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
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
     * @GetMapping(path="group/all")
     */
    public function getGroupAll()
    {
        return $this->group->all();
    }

    /**
     * @GetMapping(path="permission")
     */
    public function authority()
    {
        return AuthInit::geAuthList();
    }

    /**
     * @GetMapping(path="group/{id}")
     */
    public function groupInfo(int $id)
    {
        return $this->group->groupInfo($id);
    }

    /**
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

    }
}
