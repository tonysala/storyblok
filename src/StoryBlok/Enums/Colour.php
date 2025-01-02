<?php

namespace App\Services\StoryBlok\Enums;

enum Colour: string
{
    case NOT_SET = '';
    case WHITE = 'white';
    case BLACK = 'black';
    case ORANGE = 'orange';
    case BLUE = 'blue';
    case GREEN = 'green';
    case LILAC = 'lilac';
    case PINK = 'pink';
    case GREY = 'grey';
    case LIGHT_GREY = 'lightGrey';
    case VERY_LIGHT_GREY = 'veryLightGrey';
    case BRIGHT_WHITE = 'brightWhite';

    public function withPrefix(string $prefix): string
    {
        return match ($this) {
            self::NOT_SET => $this->value,
            default => $prefix.$this->value,
        };
    }
}
