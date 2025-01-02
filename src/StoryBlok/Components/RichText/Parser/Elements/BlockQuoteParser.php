<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\BlockQuote\BlockQuote;
use DOMElement;

class BlockQuoteParser
{
    use HasChildNodes;

    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        return [
            new BlockQuote(
                content: $this->children(),
            ),
        ];
    }
}
