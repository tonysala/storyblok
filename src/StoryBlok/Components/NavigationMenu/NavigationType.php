<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\NavigationMenu;

enum NavigationType: string
{
    case ALL = 'allItems';
    case FIRST_THREE = 'firstThree';
    case NOT_FIRST_THREE = 'notFirstThree';
}
