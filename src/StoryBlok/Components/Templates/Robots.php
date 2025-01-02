<?php

namespace App\Services\StoryBlok\Components\Templates;

enum Robots: string
{
    case ALL = 'all';
    case NONE = 'none';
    case NOINDEX = 'noindex';
    case NOFOLLOW = 'nofollow';

    public static function default(): self
    {
        return self::ALL;
    }
}
