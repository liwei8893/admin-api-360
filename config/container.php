<?php

declare(strict_types=1);

use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;

$container = new Container((new DefinitionSourceFactory())());

return \Hyperf\Context\ApplicationContext::setContainer($container);
