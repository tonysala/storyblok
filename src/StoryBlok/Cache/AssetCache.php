<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Cache;

use App\Services\StoryBlok\Api\UsesManagementApi;
use App\Services\StoryBlok\Resources\Asset;
use App\Services\StoryBlok\Resources\AssetFolder;
use Illuminate\Support\Facades\Log;

class AssetCache
{
    use UsesManagementApi;

    public static array $cache = [];

    /**
     * @param  string  $key
     * @param  \App\Services\StoryBlok\Resources\AssetFolder|int  $parent
     *
     * @return \App\Services\StoryBlok\Resources\Asset
     * @throws \App\Services\StoryBlok\Cache\AssetNotFoundException
     */
    public static function get(string $key, AssetFolder|int $parent): Asset
    {
        $parent = $parent instanceof AssetFolder ? $parent->getId() : $parent;

        try {
            return self::getFromCache($parent, $key);
        } catch (CacheMissException) {
            $page = 1;

            $foundAsset = null;

            // Get all asset in the $parent asset folder
            if (! array_key_exists($parent, static::$cache)) {
                static::$cache[$parent] = [];
                while (true) {
                    $response = self::getManagementApi()->getAssets($parent, page: $page);
                    foreach ($response->resources as $asset) {
                        if (! $foundAsset && ($asset->data['meta_data']['FID'] ?? null) === $key) {
                            $foundAsset = $asset;
                        }
                        if (! array_key_exists($asset->data['id'], static::$cache[$parent])) {
                            static::$cache[$parent][$asset->data['id']] = $asset;
                        } else {
                            // Break early as we now have the latest images
                            break;
                        }
                    }

                    if ($page < ceil($response->total / 100)) {
                        $page++;
                    } else {
                        break;
                    }
                }
            }
        }

        return $foundAsset ?? throw new AssetNotFoundException('Asset not found on StoryBlok.');
    }

    public static function store(Asset $asset, AssetFolder|int $parent): Asset
    {
        $parent = $parent instanceof AssetFolder ? $parent->getId() : $parent;

        return static::$cache[$parent][$asset->getId()] = $asset;
    }

    public static function clear(): void
    {
        static::$cache = [];
    }

    public static function getFromCache(int $parent, string $key): Asset
    {
        if (! array_key_exists($parent, static::$cache)) {
            throw new CacheMissException('No asset folder found in the cache with id: '.$parent);
        }

        $matches = array_filter(
            static::$cache[$parent],
            fn(Asset $asset) => isset($asset->data['meta_data']['FID']) && $asset->data['meta_data']['FID'] === $key,
        );

        if ($matches) {
            /** @var Asset $asset */
            $asset = array_values($matches)[0];
            Log::debug('Found existing asset ['.$asset->getId().']...');

            return $asset;
        }

        throw new CacheMissException('No asset found with id: '.$key);
    }
}
