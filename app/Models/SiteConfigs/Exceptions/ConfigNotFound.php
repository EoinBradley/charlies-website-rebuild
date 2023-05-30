<?php

namespace App\Models\SiteConfigs\Exceptions;

use Exception;
use Throwable;

class ConfigNotFound extends Exception
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withName(string $name): ConfigNotFound
    {
        return new static(sprintf("Config not found with name '%s'", $name));
    }
}
