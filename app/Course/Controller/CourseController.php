<?php

namespace App\Course\Controller;

use App\Course\Service\CourseService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Mine\Annotation\Auth;
use Mine\MineController;

#[Controller(prefix: "course"), Auth]
class CourseController extends MineController
{
    #[Inject]
    public CourseService $service;
}