<?php

namespace App\Models\Permissions;

class Role
{
    public const UPDATE_HOMEPAGE_DESCRIPTION_ROLE = 'Update homepage description';

    public const UPDATE_OPENING_HOURS_ROLE = 'Update opening hours';

    public const MANAGE_ARTISTS_ROLE = 'Manage artists';

    public const MANAGE_EVENTS_ROLE = 'Manage events';

    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {
    }
}
