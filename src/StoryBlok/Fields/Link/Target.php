<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields\Link;

enum Target: string
{
    case SELF = '_self';
    case BLANK = '_blank';
    case TOP = '_top';

    public static function fromOpenInNewTab(bool $value): self
    {
        return match ($value) {
            true => self::BLANK,
            false => self::SELF,
        };
    }
}
