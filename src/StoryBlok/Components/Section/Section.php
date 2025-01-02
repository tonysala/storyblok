<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Section;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Enums\Colour;

class Section extends Component
{
    public const NAME = 'section';

    public function __construct(
        public readonly string $title = '',
        public readonly string $slug = '',
        #[WithComponents] public readonly array $blocks = [],
        public readonly bool $fullWidth = true,
        public readonly bool $noMargin = false,
        public readonly bool $padding = false,
        public readonly bool $fullBleed = false,
        public readonly ContainerWidth $containerWidth = ContainerWidth::NOT_SET,
        public readonly Colour $backgroundColour = Colour::NOT_SET,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'width' => $this->fullWidth,
            'padding' => $this->padding,
            'no_margin' => $this->noMargin,
            'full_bleed' => $this->fullBleed,
            'container_width' => $this->containerWidth->value,
            'background_colour' => $this->backgroundColour->withPrefix('--'),
            'blocks' => $this->blocks,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            slug: $data['slug'],
            blocks: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['blocks']),
            fullWidth: $data['width'],
            noMargin: $data['no_margin'],
            padding: $data['padding'],
            fullBleed: $data['full_bleed'],
            containerWidth: ContainerWidth::tryFrom($data['container_width']),
            backgroundColour: Colour::tryFrom(str_replace('--', '', $data['background_colour'])),
        );
    }
}
