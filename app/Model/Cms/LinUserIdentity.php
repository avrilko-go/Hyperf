<?php

declare(strict_types=1);

namespace App\Model\Cms;

use App\Exception\Cms\UserException;
use App\Model\Model;
use App\Service\TokenService;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;

class LinUserIdentity extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lin_user_identity';

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
    protected $fillable = ['user_id', 'identity_type', 'identifier', 'credential'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * @Inject()
     * @var LinUser
     */
    private $user;

    /**
     * @Inject()
     * @var TokenService
     */
    private $token;

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';

    /**
     * 验证方式：用户名称
     */
    const TYPE_LOGIN_USERNAME = "USERNAME_PASSWORD";

    /**
     * 验证方式集合
     */
    const TYPE_LOGIN_MAP = [
        self::TYPE_LOGIN_USERNAME => "用户名称登陆"
    ];

    /**
     * 检测密码是否正确
     *
     * @param string $username
     * @param string $password
     * @return LinUser
     *
     * @throws UserException
     */
    public function verify(string $username, string $password, string $loginType) :LinUser
    {
        if (!isset(self::TYPE_LOGIN_MAP[$loginType])) {
            throw new UserException([
                'code' => 400,
                'msg' => '不支持该登录验证方式',
                'errorCode' => 10090
            ]);
        }


        try {
            $where = [
                'identity_type' => $loginType,
                'identifier' => $username
            ];
            $user = $this->query()->where($where)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new UserException();
        }

        if (!$this->checkPassword($password, $user->credential)) {
            throw new UserException([
                'code' => 400,
                'msg' => '密码错误，请重新输入',
                'errorCode' => 10030
            ]);
        }
        return $this->user->getUserById($user->user_id);
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
        return md5($rawPassword.config('mode.app_key')) === $password;
    }

    /**
     * 修改登陆密码
     *
     * @param string $password
     *
     * @throws \App\Exception\Cms\TokenException
     */
    public function changePassword(string $password)
    {
        $userId = $this->token->getCurrentUID();
        $this->query()->where(['user_id' => $userId, 'identity_type' => self::TYPE_LOGIN_USERNAME])->update([
            'credential' => md5($password. config('mode.app_key'))
        ]);
    }
}