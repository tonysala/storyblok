<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Fields\Category;

class DataSourceEntry
{
    /**
     * @param  array{
     *  id: int,
     *  name: string,
     *  value: string,
     *  dimension_value?: string
     * }  $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function toCategory(string $dimension): Category
    {
        return new Category(
            id: $this->data['id'],
            value: $this->data['value'],
            label: $this->data['name'],
            dimension: $dimension,
        );
    }
}
