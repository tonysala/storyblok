<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Cache;

use App\Services\StoryBlok\Api\ContentApi;
use App\Services\StoryBlok\Resources\ContentStory;

class StoryCache
{
    /**
     * @var ContentStory[]
     */
    public static array $cache = [];

    public static function fill(): void
    {
        /** @var ContentApi $api */
        $api = app(ContentApi::class);

        $page = 1;
        static::$cache = [];

        while (true) {
            $response = $api->searchStories([
                'per_page' => 100,
                'sort_by' => 'slug:asc',
                'page' => $page,
            ]);

            foreach ($response->resources as $story) {
                static::$cache[$story->getId()] = $story;
            }

            if (ceil($response->total / 100) > $page) {
                $page += 1;
            } else {
                break;
            }
        }
    }

    public static function store(ContentStory $story): ContentStory
    {
        return static::$cache[$story->getId()] = $story;
    }

    public static function clear(): void
    {
        static::$cache = [];
    }
}
