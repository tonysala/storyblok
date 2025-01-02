<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components;

interface ComponentInterface
{
    public static function getName(): string;

    public function getNested(): array;

    public function getDescendants(): array;

    public function getNestedProperties(ComponentInterface $component): array;

    public static function deserialise(array $data): self;

    public function toArray(): array;
}
