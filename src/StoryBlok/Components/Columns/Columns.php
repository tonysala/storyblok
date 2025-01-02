<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Columns;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class Columns extends Component
{
    public const NAME = 'columns';

    public function __construct(
        #[WithComponents] public readonly array $columns,
        public readonly bool $overlap = false,
        public readonly string $reference = '',
        public readonly VerticalAlignment $verticalAlignment = VerticalAlignment::INITIAL,
    ) {}

    public function toArray(): array
    {
        return [
            'Columns' => $this->columns,
            'overlap' => $this->overlap,
            'Reference' => $this->reference,
            'vertical_alignment' => $this->verticalAlignment->value,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            columns: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['Columns']),
            overlap: $data['overlap'] ?? false,
            reference: $data['Reference'] ?? '',
            verticalAlignment: isset($data['vertical_alignment'])
                ? VerticalAlignment::tryFrom($data['vertical_alignment'])
                : VerticalAlignment::INITIAL,
        );
    }
}
