<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Marks\Superscript;
use DOMElement;

class SuperscriptParser
{
    use HasChildNodes;

    public function __construct(
        public readonly DOMElement $element,
        public readonly array $inherited = [],
    ) {
    }

    public function parse(): array
    {
        $marks = [
            ...$this->inherited,
            new Superscript(),
        ];

        return $this->children($this->element, $marks);
    }
}
