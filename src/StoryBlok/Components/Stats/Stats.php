<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Stats;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Enums\Colour;

class Stats extends Component
{
    public const NAME = 'stats';

    /**
     * @property ComponentInterface[] $items
     */
    public function __construct(
        #[WithComponents] public readonly array $items,
        public readonly Colour $background = Colour::LIGHT_GREY,
    ) {}

    public function toArray(): array
    {
        return [
            'colour' => $this->background->withPrefix('stats--'),
            'stats' => $this->items,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            items: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['stats']),
            background: Colour::tryFrom(str_replace('stats--', '', $data['colour'])),
        );
    }
}
