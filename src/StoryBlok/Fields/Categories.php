<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

use JsonSerializable;

class Categories implements FieldInterface, JsonSerializable
{
    use SerializableField;

    /**
     * @param  Category[]  $categories
     * @param  \App\Services\StoryBlok\Fields\DataSource|null  $datasource
     */
    public function __construct(
        public readonly array $categories = [],
        public readonly ?DataSource $datasource = null,
    ) {}

    public function serialise(): array
    {
        return [
            'categories' => $this->categories,
            ...($this->datasource ? ['datasource' => $this->datasource] : []),
        ];
    }

    public static function deserialise(array $data): self
    {
        $categories = array_map(
            fn($category) => new Category(
                $category['id'],
                $category['value'],
                $category['label'],
                $category['dimension'],
            ),
            $data['categories'],
        );
        if ($data['datasource']) {
            $datasource = new DataSource(
                $data['datasource']['id'],
                $data['datasource']['value'],
                $data['datasource']['label'],
            );
        }

        return new self($categories, $datasource ?? null);
    }
}
