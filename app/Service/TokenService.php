<?php


declare(strict_types=1);

namespace App\Service;


use App\Exception\Cms\TokenException;
use App\Model\Cms\LinUser;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class TokenService
{
    /**
     * @Inject()
     * @var RequestInterface
     */
    private $request;

    /**
     * @Inject()
     * @var LinUser
     */
    private $user;

    /**
     * 获取token
     *
     * @param object $user
     *
     * @return array
     */
    public function getToken(object $user) :array
    {
        return  [
            'access_token' => $this->createAccessToken($user),
            'refresh_token' => $this->createRefreshToken($user)
        ];
    }

    /**
     * 创建access_token
     *
     * @param object $user
     *
     * @return string
     */
    public function createAccessToken(object $user) :string
    {
        $payload = [
            'iss' => 'avrilko', //签发者
            'iat' => time(), //什么时候签发的
            'exp' => time() + 7200, //2小时
            'user' => $user,
        ];
        return JWT::encode($payload, config('mode.app_access_token'));
    }

    /**
     * 创建refresh_token
     *
     * @param object $user
     *
     * @return string
     */
    public function createRefreshToken(object $user) :string
    {
        $payload = [
            'iss' => 'avrilko', //签发者
            'iat' => time(), //什么时候签发的
            'exp' => time() + 604800, //过期时间，一个星期
            'user' => ['id' => $user->id],
        ];
        return JWT::encode($payload, config('mode.app_refresh_token'));
    }

    /**
     * 获取用户id
     *
     * @return int
     *
     * @throws TokenException
     */
    public function getCurrentUID() :int 
    {
        $uid = (int)$this->getCurrentTokenVar('id');
        return $uid;
    }

    /**
     * 获取用户昵称
     *
     * @return string
     *
     * @throws TokenException
     */
    public function getCurrentName() : string
    {
        $userName = (string)$this->getCurrentTokenVar('username');
        return $userName;
    }

    /**
     * 从token中获取信息
     *
     * @param string $key
     * @param string $tokenType
     *
     * @return mixed
     * @throws TokenException
     */
    public function getCurrentTokenVar(string $key, string $tokenType = 'app_access_token')
    {
        $authorization = $this->request->header('authorization');
        if (!$authorization) {
            throw new TokenException(['msg' => '请求未携带Authorization信息']);
        }

        list($type, $token) = explode(' ', $authorization);
        if ($type !== 'Bearer') {
            throw new TokenException(['msg' => '接口认证方式需为Bearer']);
        }

        if (!$token || $token === 'undefined') {
            throw new TokenException(['msg' => '尝试获取的Authorization信息不存在']);
        }
        $secretKey = config("mode.{$tokenType}");
        try {
            $jwt = (array)JWT::decode($token, $secretKey, ['HS256']);
        } catch (SignatureInvalidException $e) {  //签名不正确
            throw new TokenException(['msg' => '令牌签名不正确，请确认令牌有效性或令牌类型']);
        } catch (BeforeValidException $e) {  // 签名在某个时间点之后才能用
            throw new TokenException(['msg' => '令牌尚未生效']);
        } catch (ExpiredException $e) {  // token过期
            throw new TokenException(['msg' => '令牌已过期，刷新浏览器重试', 'error_code' => 10050]);
        } catch (\Exception $e) {  //其他错误
            throw new \Exception($e->getMessage());
        }
        if (array_key_exists($key, $jwt['user'])) {
            return $jwt['user']->$key;
        } else {
            throw new TokenException(['msg' => '尝试获取的Token变量不存在']);
        }
    }

    /**
     * 获取用户权限相关信息
     *
     * @return array
     *
     * @throws TokenException
     * @throws \App\Exception\Cms\UserException
     */
    public function userAuth():array
    {
        $uid = $this->getCurrentUID();
        $user = $this->user->getUserByUID($uid);

        return $user;
    }


}
