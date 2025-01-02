<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\MatterportEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class MatterportParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        return [new Blok([new MatterportEmbed($source)])];
    }
}
