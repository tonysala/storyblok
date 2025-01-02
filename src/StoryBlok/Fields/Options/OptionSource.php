<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields\Options;

enum OptionSource
{
    case STORIES;
    case DATASOURCES;
    case JSON;
    case SELF;
    case LANGUAGES;
}
