<?php

declare(strict_types=1);

namespace App\Model\Cms;

use App\Exception\Cms\UserException;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\DbConnection\Model\Model;

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


    public static function verify(string $username, string $password)
    {
        try {
            $user = self::query()->where('username', $username)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new UserException();
        }

        if (!$user->active) {
            throw new UserException([
                'code' => 400,
                'message' => '账户已被禁用，请联系管理员',
                'errorCode' => 10070
            ]);
        }


    }

}