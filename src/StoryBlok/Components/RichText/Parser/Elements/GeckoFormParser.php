<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\GeckoFormEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class GeckoFormParser
{
    public function __construct(
        public readonly DOMElement $element,
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');
        parse_str(parse_url($source, PHP_URL_QUERY), $params);

        return [new Blok([new GeckoFormEmbed($params['uuid'])])];
    }
}
