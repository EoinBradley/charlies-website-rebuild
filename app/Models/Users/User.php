<?php

namespace App\Models\Users;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $email,
        public readonly string $hashed_password,
        public readonly string $first_name,
        public readonly string $last_name
    ) {
    }
}
