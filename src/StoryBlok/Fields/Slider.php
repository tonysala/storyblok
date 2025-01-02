<?php

namespace App\Services\StoryBlok\Fields;

use JsonSerializable;

class Slider implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public function __construct(
        public readonly int $value
    ) {
    }

    public function serialise(): array
    {
        return [
            'value' => $this->value,
            'plugin' => 'storyblok-slider',
        ];
    }
}
