<?php

namespace App\Services\StoryBlok\Components\RichText\Features\BlockQuote;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class BlockQuote implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'blockquote';

    public function __construct(
        public readonly array $content,
    ) {}

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'type' => self::NAME,
        ];
    }
}
