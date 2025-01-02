<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\InstagramEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class InstagramEmbedParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $embed = $this->element->ownerDocument->saveXML($this->element);

        return [new Blok([new InstagramEmbed($embed)])];
    }
}
