<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Cache;

use App\Services\StoryBlok\Api\UsesManagementApi;
use App\Services\StoryBlok\Resources\InternalTag;
use RuntimeException;

class InternalTagCache
{
    use UsesManagementApi;

    /**
     * @var array<int, \App\Services\StoryBlok\Resources\InternalTag>
     */
    public static array $cache = [];

    public static function get(int $id): InternalTag
    {
        $tags = array_filter(self::$cache, fn (InternalTag $tag) => $tag->data['id'] === $id);
        if (! empty($tags)) {
            return array_shift($tags);
        }

        self::fill();
        foreach (self::$cache as $tag) {
            if ($id === $tag->data['id']) {
                return $tag;
            }
        }

        throw new RuntimeException('Internal tag not found.');
    }

    public static function getByName(string $name): InternalTag
    {
        $tags = array_filter(
            self::$cache,
            fn (InternalTag $tag) => strtolower($tag->data['name']) === strtolower($name),
        );
        if (! empty($tags)) {
            return array_shift($tags);
        }

        self::fill();
        foreach (self::$cache as $tag) {
            if (strtolower($tag->data['name']) === strtolower($name)) {
                return $tag;
            }
        }

        throw new RuntimeException('Internal tag not found.');
    }

    public static function fill(): void
    {
        $page = 1;
        static::$cache = [];

        while (true) {
            $response = self::getManagementApi()->getInternalTags([
                'per_page' => 100,
                'page' => $page,
            ]);

            foreach ($response->resources as $tag) {
                static::$cache[$tag->getId()] = $tag;
            }

            if (ceil($response->total / 100) > $page) {
                $page += 1;
            } else {
                break;
            }
        }
    }

    public static function store(InternalTag $tag): InternalTag
    {
        return static::$cache[$tag->getId()] = $tag;
    }

    public static function clear(): void
    {
        static::$cache = [];
    }
}
