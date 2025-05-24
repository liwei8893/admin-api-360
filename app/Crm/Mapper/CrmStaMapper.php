<?php

namespace App\Crm\Mapper;

use Mine\Abstracts\AbstractMapper;

class CrmStaMapper extends AbstractMapper
{
    public $model;

    public function assignModel(): void
    {
        $this->model = "";
    }
}
