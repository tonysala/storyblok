<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Image;

enum Orientation: string
{
    case LANDSCAPE = 'landscape';
    case PORTRAIT = 'portrait';
}
