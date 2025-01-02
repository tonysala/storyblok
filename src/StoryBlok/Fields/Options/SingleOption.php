<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields\Options;

use App\Services\StoryBlok\Fields\FieldInterface;
use App\Services\StoryBlok\Fields\SerializableField;
use JsonSerializable;

class SingleOption implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public function __construct(
    ) {
    }

    public function serialise(): array
    {
        return [
        ];
    }
}
