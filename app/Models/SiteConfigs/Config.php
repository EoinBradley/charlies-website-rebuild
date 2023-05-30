<?php

namespace App\Models\SiteConfigs;

class Config
{
    public function __construct(
        public readonly string $id,
        public string $value
    ) {
    }
}
