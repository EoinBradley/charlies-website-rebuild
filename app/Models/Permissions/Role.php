<?php

namespace App\Models\Permissions;

class Role
{
    public const UPDATE_HOMEPAGE_DESCRIPTION_ROLE = 'Update homepage description';

    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {
    }
}
