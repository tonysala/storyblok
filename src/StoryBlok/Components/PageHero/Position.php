<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\PageHero;

enum Position: string
{
    case HEADING_ABOVE_IMAGE = 'above';
    case HEADING_ON_IMAGE = 'ontop';

    public static function default(): self
    {
        return self::HEADING_ABOVE_IMAGE;
    }
}
