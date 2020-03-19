<?php

declare(strict_types=1);

use App\Model\Cms\LinGroup;
use App\Model\Cms\LinUser;
use App\Model\Cms\LinUserGroup;
use App\Model\Cms\LinUserIdentity;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SeedAdmin extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = date('Y-m-d H:i:s');
        $admin = [
            'id' => 1,
            'username' => 'root',
            'nickname' => 'admin',
            'avatar' => '',
            'email' => '475721797@qq.com',
            'create_time' => $time,
            'update_time' => $time
        ];
        Db::table('lin_user')->insert($admin);

        $group = [
            'id' => 1,
            'name' => 'admin',
            'info' => '所有权限',
            'create_time' => $time,
            'update_time' => $time
        ];
        Db::table('lin_group')->insert($group);

        Db::table('lin_user_group')->insert([
            'id' => 1,
            'user_id' => 1,
            'group_id' => 1,
        ]);

        Db::table('lin_user_identity')->insert([
            'id' => 1,
            'user_id' => 1,
            'identity_type' => LinUserIdentity::TYPE_LOGIN_USERNAME,
            'identifier' => 'root',
            'credential' => md5('123456'.config('mode.app_key')),
            'create_time' => $time,
            'update_time' => $time
        ]);
    }
}
