<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLinFileTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lin_file', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("上传文件主键");
            $table->string("path",500)->nullable(false)->comment("上传文件的路径");
            $table->integer("type")->nullable(false)->default(1)->comment("上传文件的位置 1表示本地  其他见代码定义");
            $table->string('name',100)->nullable(false)->default("")->comment("文件的名称");
            $table->string('extension',50)->nullable(false)->default("")->comment("文件的后缀名");
            $table->integer('size')->nullable(false)->default(0)->comment('文件的大小');
            $table->string('md5',40)->nullable(false)->default("")->comment("文件的md5值的大小")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lin_file');
    }
}
