<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Table;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class Table extends Component
{
    public const NAME = 'table_2';

    public function __construct(
        #[WithComponents] public readonly array $rows = [],
        public readonly bool $showHead = false,
    ) {}

    public function toArray(): array
    {
        return [
            'rows' => $this->rows,
            'showHead' => $this->showHead,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            rows: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['rows']),
            showHead: $data['showHead'],
        );
    }
}
