<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components;

interface ComponentGroup
{
    public static function deserialise(array $data): self;

    public function toArray(): array;
}
