<?php

namespace App\Services\StoryBlok\Spaces;

use InvalidArgumentException;
use RuntimeException;

enum Space: string
{
    case WWW = 'www';
    case PORTAL = 'my';
    case GLOBAL = 'global';

    public function getHost(): string
    {
        return config('services.storyblok.spaces.'.$this->value.'.host');
    }

    public function getId(): string
    {
        return config('services.storyblok.spaces.'.$this->value.'.id');
    }

    public function getContentApiKey(): string
    {
        return config('services.storyblok.spaces.'.$this->value.'.api_key');
    }

    public function getAssetFolders(): array
    {
        return match (app()->environment()) {
            'production' => config('services.storyblok.spaces.'.$this->value.'.assets.production'),
            default => config('services.storyblok.spaces.'.$this->value.'.assets.dev'),
        };
    }

    public static function fromConnectionName(string $name): self
    {
        return match ($name) {
            'my_uea' => self::PORTAL,
            'liferay' => self::WWW,
            default => throw new RuntimeException('Invalid connection name'),
        };
    }

    public static function fromId(string|int $id): self
    {
        $spaces = config('services.storyblok.spaces');
        $key = array_search((string)$id, array_column($spaces, 'id'), true);

        if ($key === false) {
            throw new InvalidArgumentException('Invalid space id');
        }

        return self::from(array_keys($spaces)[$key]);
    }
}
