<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\Echo360;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class Echo360Parser
{
    public function __construct(
        public readonly DOMElement $element,
    ) {}

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');
        preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $source, $matches);
        return [new Blok([new Echo360($matches[1])])];
    }
}
