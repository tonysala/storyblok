<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

trait SerializableField
{
    abstract public function serialise();

    public function jsonSerialize(): mixed
    {
        return $this->serialise();
    }
}
