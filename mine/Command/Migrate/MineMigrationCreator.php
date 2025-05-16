<?php


declare(strict_types=1);

namespace Mine\Command\Migrate;

use Hyperf\Database\Migrations\MigrationCreator;

class MineMigrationCreator extends MigrationCreator
{
    public function stubPath(): string
    {
        return BASE_PATH . '/mine/Command/Migrate/Stubs';
    }
}
