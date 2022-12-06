<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateCourseSignupGradeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_signup_grade', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('年级课程配置中间表');
            $table->integer('course_signup_config_id')->comment('课程报名配置表ID');
            $table->integer('grade_id')->comment('年级ID');
            $table->unique(['course_signup_config_id', 'grade_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_signup_grade');
    }
}
