<?php

declare(strict_types=1);

namespace App\Score\Listener;

use App\Course\Model\CourseBasis;
use App\Score\Model\Avatar;
use Hyperf\Database\Model\Relations\Relation;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;

#[Listener]
class MorphMapRelationListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        Relation::morphMap([
            'avatar' => Avatar::class,
            'courseBasis' => CourseBasis::class,
        ]);
    }
}
