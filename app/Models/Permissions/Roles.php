<?php

namespace App\Models\Permissions;

use Illuminate\Support\Collection;

class Roles extends Collection
{
    public function hasAccessTo(string $role): bool
    {
        return $this
            ->where('name', $role)
            ->isNotEmpty();
    }
}
