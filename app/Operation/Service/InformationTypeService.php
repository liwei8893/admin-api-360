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

namespace App\Operation\Service;

use App\Operation\Mapper\InformationTypeMapper;
use Mine\Abstracts\AbstractService;

/**
 * 资讯分类服务类
 */
class InformationTypeService extends AbstractService
{
    /**
     * @var InformationTypeMapper
     */
    public $mapper;

    public function __construct(InformationTypeMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
