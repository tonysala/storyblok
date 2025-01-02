<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

use JsonSerializable;

class MultiAsset implements FieldInterface, JsonSerializable
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
