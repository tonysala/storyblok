<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\SylvesterEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class SylvesterParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        return [new Blok([new SylvesterEmbed()])];
    }
}
