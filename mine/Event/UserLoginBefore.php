<?php


declare(strict_types=1);

namespace Mine\Event;

class UserLoginBefore
{
    public array $inputData;

    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
    }
}
