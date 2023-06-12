<?php

namespace App\Models\Artists\Exceptions;

use Exception;
use Throwable;

class ArtistNotFound extends Exception
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withId(string $id): ArtistNotFound
    {
        return new static(sprintf("Artist not found with id '%s'", $id));
    }
}
