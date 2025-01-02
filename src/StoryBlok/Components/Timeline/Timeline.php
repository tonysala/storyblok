<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Timeline;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class Timeline extends Component
{
    public const NAME = 'timeline';

    public function __construct(
        #[WithComponents] public readonly array $items = [],
    ) {}

    public function toArray(): array
    {
        return [
            'children' => $this->items,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            items: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['children']),
        );
    }
}
