<?php

namespace App\Services\StoryBlok\Components\RichText\Features\Marks;

use App\Services\StoryBlok\Fields\SerializableField;
use JsonSerializable;

class Anchor implements JsonSerializable
{
    use SerializableField;

    public function __construct(
        public readonly string $id = '',
    ) {
    }

    public function serialise(): array
    {
        return [
            'type' => 'anchor',
            'attrs' => [
                'id' => $this->id,
            ],
        ];
    }
}
