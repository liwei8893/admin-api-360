<?php
declare(strict_types=1);


namespace App\Crm\Service;

use App\Crm\Mapper\CrmStudyRecordMapper;
use Mine\Abstracts\AbstractService;

/**
 * 学习记录服务类
 */
class CrmStudyRecordService extends AbstractService
{
    /**
     * @var CrmStudyRecordMapper
     */
    public $mapper;

    public function __construct(CrmStudyRecordMapper $mapper)
    {
        $this->mapper = $mapper;
    }

}
