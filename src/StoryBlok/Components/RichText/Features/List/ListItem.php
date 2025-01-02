<?php

namespace App\Services\StoryBlok\Components\RichText\Features\List;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class ListItem implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'list_item';

    public function __construct(
        public readonly array $content,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => self::NAME,
            'content' => $this->content,
        ];
    }
}
