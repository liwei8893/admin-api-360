<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Course\Service;

use App\Course\Mapper\CourseIndexMapper;
use Mine\Abstracts\AbstractService;

/**
 * 体验课管理服务类
 */
class CourseIndexService extends AbstractService
{
    /**
     * @var CourseIndexMapper
     */
    public $mapper;

    public function __construct(CourseIndexMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}