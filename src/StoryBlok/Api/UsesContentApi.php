<?php

namespace App\Services\StoryBlok\Api;

trait UsesContentApi
{
    public static function getContentApi(array $with = []): ContentApi
    {
        return app(ContentApi::class, $with);
    }
}
