<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\MicrosoftFormEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Fields\Slider;
use DOMElement;

class MicrosoftFormParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');
        $height = intval($this->element->getAttribute('height'));

        return [
            new Blok([
                new MicrosoftFormEmbed(
                    $source,
                    new Slider($height ?: 1500)
                ),
            ]),
        ];
    }
}
