<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Table;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class TableRow extends Component
{
    public const NAME = 'table_row';

    public function __construct(
        #[WithComponents] public readonly array $cells = [],
    ) {}

    public function toArray(): array
    {
        return [
            'cells' => $this->cells,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            cells: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['cells']),
        );
    }
}
