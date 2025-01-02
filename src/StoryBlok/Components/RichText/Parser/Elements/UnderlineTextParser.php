<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Marks\Underline;
use DOMElement;
use DOMText;

class UnderlineTextParser
{
    use HasChildNodes;

    public function __construct(
        public readonly DOMText|DOMElement $element,
        public readonly array $inherited = [],
    ) {
    }

    public function parse(): array
    {
        $marks = [
            ...$this->inherited,
            new Underline(),
        ];

        return $this->children($this->element, $marks);
    }
}
