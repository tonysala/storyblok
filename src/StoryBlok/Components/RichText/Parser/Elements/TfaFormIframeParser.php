<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\TfaFormEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class TfaFormIframeParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');
        $id = substr($source, strrpos($source, '/') + 1);

        return [new Blok([new TfaFormEmbed($id)])];
    }
}
