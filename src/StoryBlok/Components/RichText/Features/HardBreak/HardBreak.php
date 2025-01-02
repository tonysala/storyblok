<?php

namespace App\Services\StoryBlok\Components\RichText\Features\HardBreak;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class HardBreak implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'hard_break';

    public function __construct() {}

    public function toArray(): array
    {
        return [
            'type' => self::NAME,
        ];
    }
}
