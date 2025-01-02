<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Image;

enum AspectRatio: string
{
    case SQUARE = '1:1';
    case RECTANGLE = '4:3';
    case WIDE = '16:9';
    case EXTRAWIDE = '2:1';
    case NONE = 'unset';
}
