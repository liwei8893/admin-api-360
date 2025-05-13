<?php

namespace Mine;

use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\ApiDocs\Annotation\ApiVariable;

class GlobalResponse
{
    #[ApiModelProperty('状态码')]
    public int $code = 200;

    #[ApiModelProperty('是否成功')]
    public bool $success = true;

    #[ApiModelProperty('数据')]
    #[ApiVariable]
    public mixed $data;

    #[ApiModelProperty('信息')]
    public string $message = '';
    
    public function __construct(mixed $data)
    {
        $this->data = $data;
    }
}
