<?php

namespace App\Models\Events;

use App\Models\Artists\Artist;
use Carbon\CarbonInterface;

class Event
{
    public function __construct(
        public ?int $id,
        public readonly Artist $artist,
        public readonly CarbonInterface $startAt
    ) {
    }

    public function isEqual(Event $event): bool
    {
        return $this->startAt->format('Y-m-d H:i:s') === $event->startAt->format('Y-m-d H:i:s')
            && $this->artist->id === $event->artist->id;
    }

    public function isNotEqual(Event $event): bool
    {
        return !$this->isEqual($event);
    }
}
