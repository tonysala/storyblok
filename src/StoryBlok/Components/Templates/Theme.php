<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Templates;

enum Theme: string
{
    case ONE = 'theme__www';
    case TWO = 'theme_www-v2';
    case PORTAL = 'theme__myuea';

    public static function default(): self
    {
        return self::ONE;
    }
}
