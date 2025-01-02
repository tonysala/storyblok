<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Accordion;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Enums\Colour;

class Accordion extends Component
{
    public const NAME = 'accordion';

    public function __construct(
        #[WithComponents] public readonly array $items,
        public readonly Colour $colour = Colour::BLUE,
    ) {}

    public function toArray(): array
    {
        return [
            'accentColour' => $this->colour->withPrefix('--'),
            'accordionItems' => $this->items,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            items: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['accordionItems']),
            colour: Colour::tryFrom(str_replace('--', '', $data['accentColour'] ?? '')),
        );
    }
}
