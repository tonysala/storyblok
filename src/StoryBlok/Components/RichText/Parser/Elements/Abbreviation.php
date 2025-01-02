<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use DOMElement;

class Abbreviation
{
    use HasChildNodes;

    public function __construct(
        public readonly DOMElement $element,
        public readonly array $inherited = [],
    ) {}

    public function parse(): array
    {
        // Attributes
        $this->element->getAttribute('title');
        return $this->children(inherited: [...$this->inherited]);
    }
}
