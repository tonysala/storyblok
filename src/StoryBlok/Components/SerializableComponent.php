<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components;

trait SerializableComponent
{
    abstract public function toArray(): array;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
