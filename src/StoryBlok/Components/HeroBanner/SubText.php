<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\HeroBanner;

use App\Services\StoryBlok\Components\ComponentGroup;
use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Link\Link;

class SubText implements ComponentGroup
{
    public function __construct(
        public readonly string $text = '',
        public readonly ?Colour $backgroundColour = Colour::BLACK,
        public readonly ?Link $link = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'subtext' => $this->text,
            'subtextBackground' => $this->backgroundColour?->withPrefix('--'),
            'subtextLink' => $this->link?->serialise(),
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            text: $data['subtext'] ?? '',
            backgroundColour: Colour::from(str_replace('--', '', $data['subtextBackground'] ?? '')),
            link: isset($data['subtextLink']) ? Link::deserialise($data['subtextLink']) : null,
        );
    }
}
