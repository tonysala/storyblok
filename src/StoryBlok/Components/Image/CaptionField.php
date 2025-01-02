<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Image;

enum CaptionField: string
{
    case NONE = 'no';
    case TITLE = 'title';
    case ALT = 'alt';
    case COPYRIGHT = 'copyright';
}
