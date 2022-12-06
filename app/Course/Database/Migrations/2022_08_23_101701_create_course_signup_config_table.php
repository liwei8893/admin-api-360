<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateCourseSignupConfigTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_signup_config', static function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('课程报名配置表');
            $table->increments('id')->comment('主键');
            $table->string('title', 255)->nullable()->comment('课程名称');
            $table->integer('price')->comment('金额');
            $table->integer('day')->comment('天数');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->integer('created_by')->nullable()->comment('创建者');
            $table->integer('updated_by')->nullable()->comment('更新者');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_signup_config');
    }
}
