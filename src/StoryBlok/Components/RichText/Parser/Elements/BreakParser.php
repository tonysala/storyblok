<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\HardBreak\HardBreak;
use DOMElement;

class BreakParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        return [
            new HardBreak(),
        ];
    }
}
