<?php

namespace App\Services\StoryBlok\Fields;

use App\Services\StoryBlok\Resources\DataSource as DataSourceResource;
use JsonSerializable;

readonly class DataSource implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $value,
        public string $label,
    ) {}

    public static function fromResource(DataSourceResource $datasource): self
    {
        return new self(
            id: $datasource->getId(),
            value: $datasource->getSlug(),
            label: $datasource->getName(),
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,
        ];
    }
}
