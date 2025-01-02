<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\DirectBookEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;
use DOMXPath;

class DirectBookParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $xpath = new DOMXPath($this->element->ownerDocument);

        $code = $xpath->evaluate('string(//div[@class="ibe"]/@data-channelcode)');

        return [
            new Blok([new DirectBookEmbed($code ?: 'broadviewlodgedirect')]),
        ];
    }
}
