<?php

namespace App\Services\StoryBlok\Navigation;

interface NavigationStorage
{
    public function get(string $file): array;

    public function put(string $file, mixed $data, $options = []): void;
}
