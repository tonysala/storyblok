<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\CollectionsCarousel;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class CollectionsCarousel extends Component
{
    public const NAME = 'SB018-2';

    public function __construct(
        public readonly string $collectionId,
        public readonly int $limit = 12,
        public readonly int $columns = 4,
        public readonly bool $showAsGrid = false,
    ) {}

    public function toArray(): array
    {
        return [
            'collectionsId' => $this->collectionId,
            'showAsGrid' => $this->showAsGrid,
            'limit' => (string)$this->limit,
            'columns' => (string)$this->columns,
            'carousel_type' => 'collections',
            'show_pagination_dots' => false,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            collectionId: $data['collectionsId'],
            limit: $data['limit'] ? (int)$data['limit'] : 12,
            columns: $data['columns'] ? (int)$data['columns'] : 4,
            showAsGrid: $data['showAsGrid'],
        );
    }
}
