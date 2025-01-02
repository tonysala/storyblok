<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Marks\Italic;
use DOMElement;
use DOMText;

class ItalicTextParser
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
            new Italic(),
        ];

        return $this->children($this->element, $marks);
    }
}
