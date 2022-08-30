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

namespace App\Users\Service;

use App\Users\Mapper\UserCourseRecordMapper;
use Mine\Abstracts\AbstractService;

/**
 * 听课记录服务类
 */
class UserCourseRecordService extends AbstractService
{
    /**
     * @var UserCourseRecordMapper
     */
    public $mapper;

    public function __construct(UserCourseRecordMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    protected function handleExportData(array &$data): void
    {
        foreach ($data as &$item) {
            $item['watch_time'] =round($item['watch_time'] / 60).'分钟';
            $item['video_duration'] =round($item['video_duration'] / 60).'分钟';
            $item['timeRate'] .= '%';
        }
    }


}