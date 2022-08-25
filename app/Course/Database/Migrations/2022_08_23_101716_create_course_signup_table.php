<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

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
            $table->unique(['course_signup_config_id','course_id']);
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
