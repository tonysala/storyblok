<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\PageHero;

enum Size: string
{
    case SMALL = 'small';
    case DEFAULT = 'default';
    case LARGE = 'large';

    public static function default(): self
    {
        return self::DEFAULT;
    }
}
