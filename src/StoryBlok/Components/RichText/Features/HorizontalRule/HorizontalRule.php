<?php

namespace App\Services\StoryBlok\Components\RichText\Features\HorizontalRule;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class HorizontalRule implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'horizontal_rule';

    public function __construct() {}

    public function toArray(): array
    {
        return [
            'type' => self::NAME,
        ];
    }
}
