<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLinLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lin_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("日志表主键");
            $table->string('message',450)->nullable(false)->default("")->comment("日志内容");
            $table->bigInteger("user_id")->nullable(false)->comment('用户角色表主键')->index();
            $table->string("username",50)->nullable(false)->comment("用户名称");
            $table->smallInteger("status_code")->nullable(false)->default(200)->comment("http状态码");
            $table->string("method",20)->nullable(false)->default("GET")->comment("http方法");
            $table->string("path",50)->nullable(false)->default("")->comment("请求的url path");
            $table->string('params',500)->default("")->comment("所有请求的参数");
            $table->dateTime('create_time');
            $table->dateTime('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lin_log');
    }
}
