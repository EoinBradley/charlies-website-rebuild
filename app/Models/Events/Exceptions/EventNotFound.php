<?php

namespace App\Models\Events\Exceptions;

use Exception;
use Throwable;

class EventNotFound extends Exception
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withId(string $id): EventNotFound
    {
        return new static(sprintf("Event not found with id '%s'", $id));
    }
}
