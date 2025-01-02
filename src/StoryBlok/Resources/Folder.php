<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

class Folder
{
    public function __construct(
        public readonly array $data
    ) {
    }

    public function getId(): int
    {
        return $this->data['id'];
    }
}
