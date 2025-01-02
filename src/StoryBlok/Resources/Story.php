<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

/**
 * @property array $data
 */
interface Story
{
    public function getId(): int;

    public function getData(): array;

    public function refresh(): self;
}
