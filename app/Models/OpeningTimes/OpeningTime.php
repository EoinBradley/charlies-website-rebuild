<?php

namespace App\Models\OpeningTimes;

use Carbon\Carbon;

class OpeningTime
{
    public function __construct(
        public readonly ?Carbon $openedAt,
        public readonly ?Carbon $closedAt,
        public readonly Carbon $date,
        public readonly bool $isException = false,
        public readonly ?string $exceptionDescription = null
    ) {
    }

    public function isEqual(OpeningTime $day): bool
    {
        return $this->openedAt?->format('H:i') === $day->openedAt?->format('H:i')
            && $this->closedAt?->format('H:i') === $day->closedAt?->format('H:i')
            && $this->date->dayOfWeek === $day->date->dayOfWeek;
    }

    public function isNotEqual(OpeningTime $day): bool
    {
        return !$this->isEqual($day);
    }
}
