<?php
declare(strict_types=1);


namespace App\Course\Service;

use App\Course\Mapper\CoursePeriodsFileMapper;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

/**
 * 章节文件服务类
 */
class CoursePeriodsFileService extends AbstractService
{
    /**
     * @var CoursePeriodsFileMapper
     */
    public $mapper;

    public function __construct(CoursePeriodsFileMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        // file_id,periods_id是联合唯一键, 不能重复添加,提前检测是否有数据,如果有提示错误
        $check = $this->mapper->checkFilePeriods($data['file_id'], $data['periods_id']);
        if ($check) {
            throw new NormalStatusException('该文件已添加, 请勿重复添加');
        }
        $data['created_id'] = user()->getId();
        return parent::save($data);
    }


}
