<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Components\Video\Video;
use DOMElement;

class VideoEmbedParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        if ($this->element->hasAttribute('data-playlist')) {
            $source = 'https://www.youtube.com/playlist?list='.$this->element->getAttribute('data-playlist');
        } elseif ($this->element->hasAttribute('data-lazy')) {
            $source = $this->element->getAttribute('data-lazy');
        } else {
            $source = $this->element->getAttribute('src');
        }

        return [
            new Blok([new Video($source)]),
        ];
    }
}
