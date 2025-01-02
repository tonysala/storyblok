<?php

namespace App\Services\StoryBlok\Navigation;

use Illuminate\Filesystem\FilesystemAdapter;

readonly class S3NavigationStorage implements NavigationStorage
{
    public function __construct(
        public FilesystemAdapter $disk,
        public string $environment,
    ) {}

    public function get(string $file): array
    {
        return $this->disk->json($this->getPrefix().$file, JSON_OBJECT_AS_ARRAY);
    }

    public function put(string $file, mixed $data, $options = []): void
    {
        $this->disk->put($this->getPrefix().$file, $data, $options);
    }

    protected function getPrefix(): string
    {
        return match ($this->environment) {
            'production' => 'prod/',
            'uat' => 'uat/',
            default => 'test/',
        };
    }
}
