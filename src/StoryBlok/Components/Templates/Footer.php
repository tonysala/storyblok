<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Templates;

enum Footer: string
{
    case DARK = 'dark';
    case LIGHT = 'light';

    public static function default(): self
    {
        return self::DARK;
    }
}
