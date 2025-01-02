<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

interface FieldInterface
{
    public function serialise(): mixed;
}
