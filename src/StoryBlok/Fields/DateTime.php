<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

use JsonSerializable;

class DateTime implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public function __construct(
    ) {
    }

    public function serialise(): array
    {
        return [];
    }
}
