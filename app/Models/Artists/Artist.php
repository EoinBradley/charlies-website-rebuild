<?php

namespace App\Models\Artists;

class Artist
{
    public function __construct(
        public ?int $id,
        public readonly string $name,
        public readonly string $description
    ) {
    }

    public function isEqual(Artist $artist): bool
    {
        return $this->name === $artist->name
            && $this->description === $artist->description;
    }

    public function isNotEqual(Artist $artist): bool
    {
        return !$this->isEqual($artist);
    }
}
