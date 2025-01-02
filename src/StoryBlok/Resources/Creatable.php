<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

interface Creatable
{
    public static function create(array $data): self;
}
