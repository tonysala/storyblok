<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Section;

enum ContainerWidth: string
{
    case NOT_SET = '';
    case STANDARD = 'max-w-[1280px]';
    case OVERSIZED = 'max-w-[1536px]';
    case FULL_WIDTH = 'max-w-full';
}
