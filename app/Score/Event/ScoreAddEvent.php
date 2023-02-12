<?php

declare(strict_types=1);

namespace App\Score\Event;

class ScoreAddEvent
{
    /**
     * @var string share | question | course | auth | init
     */
    public string $type;

    public int $userId;

    public int $originId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $type, int $userId, int $originId)
    {
        $this->type = $type;
        $this->userId = $userId;
        $this->originId = $originId;
    }
}
