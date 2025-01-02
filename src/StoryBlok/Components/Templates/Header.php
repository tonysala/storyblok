<?php

namespace App\Services\StoryBlok\Components\Templates;

enum Header: string
{
    case DARK = 'dark';
    case LIGHT = 'light';
    case TRANSPARENT = 'transparent';

    public static function default(): self
    {
        return self::LIGHT;
    }
}
