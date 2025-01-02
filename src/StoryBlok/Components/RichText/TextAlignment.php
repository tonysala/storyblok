<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText;

enum TextAlignment: string
{
    case NOT_SET = '';
    case LEFT = 'left';
    case RIGHT = 'right';
    case CENTER = 'center';
}
