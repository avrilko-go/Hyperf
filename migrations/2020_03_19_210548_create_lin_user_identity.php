<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLinUserIdentity extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lin_user_identity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable(false)->comment('用户id')->index();
            $table->string('identity_type',100)->nullable(false)->comment('登录类型（手机号 邮箱 用户名）或第三方应用名称（微信 微博等）');
            $table->string('identifier',100)->nullable(false)->comment('标识（手机号 邮箱 用户名或第三方应用的唯一标识）');
            $table->string('credential',100)->nullable(false)->comment('密码凭证（站内的保存密码，站外的不保存或保存token）');
            $table->dateTime('create_time');
            $table->dateTime('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lin_user_identity');
    }
}
