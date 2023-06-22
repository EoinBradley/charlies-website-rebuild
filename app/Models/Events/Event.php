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
}
