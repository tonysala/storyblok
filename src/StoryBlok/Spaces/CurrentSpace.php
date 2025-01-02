<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Spaces;

use App\Services\StoryBlok\Cache\AssetCache;
use App\Services\StoryBlok\Cache\AssetFolderCache;
use App\Services\StoryBlok\Cache\InternalTagCache;
use App\Services\StoryBlok\Cache\StoryCache;
use App\Services\StoryBlok\Cache\TagCache;
use RuntimeException;

final class CurrentSpace
{
    private static ?Space $current = null;

    public static function get(): Space
    {
        return self::$current ?? Space::WWW;
    }

    public static function set(Space $space): void
    {
        if (self::$current !== null) {
            throw new RuntimeException('Current space is already set and cannot be changed.');
        }
        self::$current = $space;
    }

    public static function change(Space $space): void
    {
        if (self::$current !== $space) {
            self::clearCache();
        }
        self::$current = $space;
    }

    public static function reset(): void
    {
        self::clearCache();
        self::$current = null;
    }

    protected static function clearCache(): void
    {
        AssetCache::clear();
        AssetFolderCache::clear();
        StoryCache::clear();
        TagCache::clear();
        InternalTagCache::clear();
    }

    public static function switch(): void
    {
        self::$current = match (self::$current) {
            Space::WWW => Space::PORTAL,
            Space::PORTAL => Space::WWW,
            default => throw new RuntimeException('Cannot switch space.'),
        };
    }
}
