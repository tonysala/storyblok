<?php

namespace App\Services\StoryBlok\Components\RichText\Features\Paragraph;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class Paragraph implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'paragraph';

    public function __construct(
        public readonly array $children,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => 'paragraph',
            'content' => $this->children,
        ];
    }
}
