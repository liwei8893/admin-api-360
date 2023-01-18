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
    public string $subjectField = 'subject';

    public string $gradeField = 'grade';

    public string $courseField = 'courseId';
}
