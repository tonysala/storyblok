<?php

namespace App\Services\StoryBlok\Components\RichText\Features\List;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class OrderedList implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'ordered_list';

    public function __construct(
        public readonly array $items,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => self::NAME,
            'content' => $this->items,
        ];
    }
}
