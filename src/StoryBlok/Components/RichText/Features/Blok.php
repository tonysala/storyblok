<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Features;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\SerializableComponent;
use JsonSerializable;

class Blok implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'blok';

    public function __construct(
        #[WithComponents] public readonly array $components,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => self::NAME,
            'attrs' => [
                'body' => $this->components,
            ],
        ];
    }
}
