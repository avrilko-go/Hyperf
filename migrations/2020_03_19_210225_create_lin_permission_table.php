<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLinPermissionTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lin_permission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100)->nullable(false)->comment("权限名称")->unique();
            $table->string('module',100)->nullable(false)->comment("所属模块");
            $table->dateTime('create_time');
            $table->dateTime('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lin_lin_permission');
    }
}
