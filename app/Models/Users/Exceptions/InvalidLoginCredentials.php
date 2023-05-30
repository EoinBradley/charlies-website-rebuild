<?php

namespace App\Models\Users\Exceptions;

use Exception;

class InvalidLoginCredentials extends Exception
{
    public function __construct(string $message = "Email & Password does not match with our record.")
    {
        parent::__construct($message);
    }
}
