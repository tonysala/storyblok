<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields\Link;

enum LinkType: string
{
    case STORY = 'story';
    case ASSET = 'asset';
    case URL = 'url';
    case MAILTO = 'email';
}
