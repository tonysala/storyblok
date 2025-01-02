<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Text\Text;
use DOMElement;
use DOMText;

class TextParser
{
    public function __construct(
        public readonly DOMText|DOMElement $text,
        public readonly array $inherited = [],
    ) {
    }

    public function parse(): array
    {
        return [
            new Text(
                text: $this->text->textContent,
                marks: $this->inherited,
            ),
        ];
    }
}
