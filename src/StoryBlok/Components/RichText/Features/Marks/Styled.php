<?php

namespace App\Services\StoryBlok\Components\RichText\Features\Marks;

use App\Services\StoryBlok\Fields\SerializableField;
use JsonSerializable;

class Styled implements JsonSerializable
{
    use SerializableField;

    public function __construct(
        public readonly string $class,
    ) {}

    public function serialise(): array
    {
        return [
            'type' => 'styled',
            'attrs' => [
                'class' => $this->class,
            ],
        ];
    }
}
