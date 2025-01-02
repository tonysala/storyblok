<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Cache;

use App\Services\StoryBlok\Api\UsesContentApi;
use App\Services\StoryBlok\Resources\Tag;
use RuntimeException;

class TagCache
{
    use UsesContentApi;

    /**
     * @var array<string, \App\Services\StoryBlok\Resources\Tag>
     */
    public static array $cache = [];

    public static function get(string $name): Tag
    {
        if (array_key_exists($name, static::$cache)) {
            return static::$cache[$name];
        }

        $tags = self::getContentApi()->getTags();
        $match = false;
        foreach ($tags as $tag) {
            if ($tag->getName() === $name) {
                $match = true;
            }
            static::$cache[$tag->getName()] = $tag;
        }

        if ($match) {
            return self::$cache[$name];
        }

        throw new RuntimeException('Internal tag not found.');
    }

    public static function fill(): void
    {
        static::$cache = [];

        $tags = self::getContentApi()->getTags();

        foreach ($tags as $tag) {
            static::$cache[$tag->getName()] = $tag;
        }
    }

    public static function store(Tag $tag): Tag
    {
        return static::$cache[$tag->getName()] = $tag;
    }

    public static function clear(): void
    {
        static::$cache = [];
    }
}
