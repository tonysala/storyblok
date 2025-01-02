<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

class DataSource
{
    /**
     * @param  array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     dimensions: array{
     *         id: int,
     *         entry_value: string,
     *         name: string
     *     }[]
     * }  $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function getSlug(): string
    {
        return $this->data['slug'];
    }
}
