<?php

namespace App\Services\StoryBlok\Api;

trait UsesManagementApi
{
    public static function getManagementApi(array $with = []): ManagementApi
    {
        return app(ManagementApi::class, $with);
    }
}
