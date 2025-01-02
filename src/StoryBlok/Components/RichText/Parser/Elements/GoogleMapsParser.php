<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\GoogleMapsEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class GoogleMapsParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');
        $parts = parse_url($source);
        parse_str($parts['query'] ?? '', $query);

        return [
            new Blok([
                new GoogleMapsEmbed(
                    id: $query['mid'] ?? '',
                    embed: isset($query['pb']) ? $source : ''
                ),
            ]),
        ];
    }
}
