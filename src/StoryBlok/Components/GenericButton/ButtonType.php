<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\GenericButton;

enum ButtonType: string
{
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case LINK = 'plain';
}
