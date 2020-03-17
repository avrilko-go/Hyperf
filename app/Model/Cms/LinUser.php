<?php

declare(strict_types=1);

namespace App\Model\Cms;

use App\Exception\Cms\UserException;
use App\Model\Model;
use App\Util\Util;
use Hyperf\Database\Model\ModelNotFoundException;
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
    protected $fillable = [];

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
     * @var LinAuth
     */
    private $auth;

    /**
     * 检测密码是否正确
     *
     * @param string $username
     * @param string $password
     * @return object
     *
     * @throws UserException
     */
    public function verify(string $username, string $password) :object
    {
        try {
            $user = $this->query()->where('username', $username)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new UserException();
        }

        if (!$user->active) {
            throw new UserException([
                'code' => 400,
                'msg' => '账户已被禁用，请联系管理员',
                'errorCode' => 10070
            ]);
        }
        if (!$this->checkPassword($password, $user->password)) {
            throw new UserException([
                'code' => 400,
                'msg' => '密码错误，请重新输入',
                'errorCode' => 10030
            ]);
        }
        return $user->setHidden(['password']);
    }

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
        var_dump(md5($rawPassword.config('mode.app_key')));
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
    public function getUserByUID($uid) :array
    {
        try {
            $user = $this->query()->findOrFail($uid)->setHidden(['password'])->toArray();
        } catch (\Exception $ex) {
            throw new UserException();
        }

        $groupName = '';
        if (!empty($user['group_id'])) {
            $group = $this->group->query()->where('id', $user['group_id'])->select(['name'])->find();
            $groupName = $group['name'];
        }
        $user['group_name'] = $groupName;

        $auths = $this->auth->getAuthByGroupID($user['group_id']);
        $auths = empty($auths) ? [] : Util::splitModules($auths);
        $user['auths'] = $auths;

        return $user;
    }

}