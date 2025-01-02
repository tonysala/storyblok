<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\NavigationMenu;

enum NavigationDepth: string
{
    case ZERO = '0';
    case ONE = '1';
    case TWO = '2';
    case THREE = '3';
    case FOUR = '4';
}
