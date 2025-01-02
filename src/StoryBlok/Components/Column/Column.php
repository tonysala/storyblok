<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Column;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class Column extends Component
{
    public const NAME = 'column';

    public function __construct(
        #[WithComponents] public readonly array $blocks,
        public readonly Width $width = Width::TWELVE,
        public readonly Order $order = Order::ONE,
        public readonly bool $divider = false,
    ) {}

    public function toArray(): array
    {
        return [
            'order' => $this->order->value,
            'width' => $this->width->value,
            'divider' => $this->divider,
            'blocks' => $this->blocks,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            blocks: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['blocks']),
            width: Width::tryFrom($data['width']),
            order: isset($data['order']) ? Order::tryFrom($data['order']) : Order::ONE,
            divider: $data['divider'] ?? false,
        );
    }
}
