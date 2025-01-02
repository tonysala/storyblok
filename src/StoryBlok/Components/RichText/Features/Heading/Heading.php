<?php

namespace App\Services\StoryBlok\Components\RichText\Features\Heading;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class Heading implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'heading';

    public function __construct(
        public readonly array $content,
        public readonly HeadingLevel $level,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => self::NAME,
            'attrs' => [
                'level' => $this->level->value,
            ],
            'content' => $this->content,
        ];
    }
}
