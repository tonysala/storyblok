<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Grid;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class Grid extends Component
{
    public const NAME = 'grid';

    public function __construct(
        #[WithComponents] public readonly array $columns,
        public readonly bool $overlap = false,
        public readonly bool $freeForm = false,
        public readonly Alignment $alignment = Alignment::LEFT,
        public readonly Size $size = Size::NOT_SET,
    ) {}

    public function toArray(): array
    {
        return [
            'columns' => $this->columns,
            'overlap' => $this->overlap,
            'alignment' => $this->alignment->value,
            'freeForm' => $this->freeForm,
            'size' => $this->size,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            columns: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['columns']),
            overlap: $data['overlap'] ?? false,
            freeForm: $data['freeForm'] ?? false,
            alignment: isset($data['alignment']) ? Alignment::tryFrom($data['alignment']) : Alignment::LEFT,
            size: isset($data['size']) ? Size::tryFrom($data['size']) : Size::NOT_SET,
        );
    }
}
