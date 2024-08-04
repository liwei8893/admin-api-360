<?php

namespace App\Play\Request;

use Mine\MineFormRequest;

class PlayAppRequest extends MineFormRequest
{
    public function getIdiomRules(): array
    {
        return [
            'id' => 'required',
        ];
    }

    public function getWordRules(): array
    {
        return [
            'id' => 'required',
        ];
    }
}
