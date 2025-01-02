<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

class Preset
{
    public function __construct(
        public array $data,
    ) {
    }

    public function getId(): int
    {
        return $this->data['id'];
    }
}
