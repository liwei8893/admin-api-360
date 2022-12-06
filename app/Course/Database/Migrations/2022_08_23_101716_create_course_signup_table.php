<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateCourseSignupTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_signup', static function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('课程报名配置中间表');
            $table->integer('course_signup_config_id')->comment('课程报名配置表ID');
            $table->integer('course_id')->comment('课程表ID');
            $table->unique(['course_signup_config_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_signup');
    }
}
