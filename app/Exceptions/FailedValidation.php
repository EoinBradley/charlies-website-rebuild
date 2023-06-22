<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

class FailedValidation extends Exception
{
    public function __construct(public readonly MessageBag $errors)
    {
        parent::__construct();
    }

    public static function withErrors(MessageBag $errors): FailedValidation
    {
        return new static($errors);
    }
}
