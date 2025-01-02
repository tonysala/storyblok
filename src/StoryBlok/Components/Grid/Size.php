<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Grid;

enum Size: string
{
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';
    case NOT_SET = '';
}
