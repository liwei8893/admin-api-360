<?php

declare(strict_types=1);

namespace Mine\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 分科权限认证
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class SubjectAuth extends AbstractAnnotation
{
    /**
     * @param string $subjectField 科目字段
     * @param string $gradeField 年级字段
     * @param string $courseField 课程ID字段
     * @param string $periodField 章节ID字段
     */
    public function __construct(
        public string $subjectField = 'subject',
        public string $gradeField = 'grade',
        public string $courseField = 'courseId',
        public string $periodField = 'periodId'
    ) {}
}
