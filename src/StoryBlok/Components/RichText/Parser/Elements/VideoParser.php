<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Components\Video\Video;
use DOMElement;
use DOMXPath;

class VideoParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        if (! $source) {
            $xpath = new DOMXPath($this->element->ownerDocument);
            if ($element = $xpath->query('./source', $this->element)->item(0)) {
                /** @var DOMElement $element */
                $source = $element->getAttribute('src');
            }
        }

        return [
            new Blok([
                new Video($source),
            ]),
        ];
    }
}
