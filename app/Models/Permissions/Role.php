<?php

namespace App\Models\Permissions;

class Role
{
    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {
    }
}
