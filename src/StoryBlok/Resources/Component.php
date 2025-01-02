<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\ManagementApi;

class Component
{
    public function __construct(
        public array $data,
        protected bool $deleted = false,
    ) {
    }

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function refresh(): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class, ['space' => config('services.storyblok.spaces.global')]);

        return $api->getComponent((string) $this->data['id']);
    }

    public function __serialize(): array
    {
        return [$this->data['id']];
    }

    public function __unserialize(array $data): void
    {
        [$id] = $data;
        $this->data['id'] = $id;
        $this->data = $this->refresh()->data;
    }
}
