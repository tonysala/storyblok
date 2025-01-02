<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Api;

use Illuminate\Support\Facades\Cache;

class ApiRateLimiter
{
    protected int $limit = 6;

    protected int $interval = 1;

    public function canMakeRequest(): bool
    {
        $key = 'storyblok_rate_limit_'.time();
        $count = Cache::get($key, 0);

        if ($count >= $this->limit) {
            return false;
        }

        Cache::put($key, $count + 1, $this->interval);

        return true;
    }
}
