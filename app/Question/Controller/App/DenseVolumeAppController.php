<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Service\DenseVolumeService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Mine\MineController;

#[Controller(prefix: 'question/app/denseVolume')]
class DenseVolumeAppController extends MineController
{
    #[Inject]
    protected DenseVolumeService $service;
}
