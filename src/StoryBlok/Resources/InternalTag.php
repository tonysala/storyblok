<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

class InternalTag
{
    public function __construct(
        public readonly array $data,
    ) {
    }

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
