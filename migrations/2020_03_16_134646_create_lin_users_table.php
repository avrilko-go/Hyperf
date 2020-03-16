<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLinUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lin_user', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("人员角色主键");
            $table->string('nickname',20)->nullable(false)->default("")->comment("昵称");
            $table->string('username',20)->nullable(false)->comment("用户名")->unique();
            $table->string('avatar',255)->nullable(false)->default("")->comment("头像url");
            $table->tinyInteger('admin')->default(0)->comment("是否是超级管理员");
            $table->tinyInteger('active')->default(1)->comment("是否是激活状态");
            $table->string("email",255)->nullable(false)->default("")->comment("邮箱地址")->unique();
            $table->bigInteger("group_id")->nullable(false)->default(0)->comment("用户组（关联group表主键），超级管理员为0");
            $table->string('password',100)->nullable(false)->comment("密码");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lin_user');
    }
}
