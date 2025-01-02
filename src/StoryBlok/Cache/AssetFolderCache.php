<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Cache;

use App\Services\StoryBlok\Api\UsesManagementApi;
use App\Services\StoryBlok\Resources\AssetFolder;

class AssetFolderCache
{
    use UsesManagementApi;

    public static array $cache = [];

    public static function fill(): void
    {
        static::$cache = [];

        foreach (self::getManagementApi()->getAssetFolders() as $folder) {
            static::$cache[$folder->getId()] = $folder;
        }
    }

    public static function store(AssetFolder $folder): AssetFolder
    {
        return static::$cache[$folder->getId()] = $folder;
    }

    public static function clear(): void
    {
        static::$cache = [];
    }
}
