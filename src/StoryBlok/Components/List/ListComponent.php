<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\List;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class ListComponent extends Component
{
    public const NAME = 'SB015_1';

    public function __construct(
        #[WithComponents] public readonly array $items,
        public readonly bool $showNumbering,
    ) {}

    public function toArray(): array
    {
        return [
            'listItems' => $this->items,
            'showNumbering' => $this->showNumbering,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            items: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['listItems']),
            showNumbering: $data['showNumbering'],
        );
    }
}
