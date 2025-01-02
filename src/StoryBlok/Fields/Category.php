<?php

namespace App\Services\StoryBlok\Fields;

use JsonSerializable;

readonly class Category implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $value,
        public string $label,
        public ?string $dimension = null,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,
            'dimension' => $this->dimension,
        ];
    }

    public function __toString(): string
    {
        return sprintf('category_%s_%s', $this->dimension, $this->value);
    }
}
