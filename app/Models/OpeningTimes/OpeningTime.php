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
}
