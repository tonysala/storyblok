<?php

namespace App\Services\StoryBlok\Components\RichText\Features\Text;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class Text implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'text';

    public function __construct(
        public readonly string $text,
        public readonly array $marks = [],
    ) {}

    public function toArray(): array
    {
        return [
            'type' => 'text',
            'marks' => $this->marks,
            'text' => $this->text,
        ];
    }
}
