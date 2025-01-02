<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Paragraph\Paragraph;
use DOMElement;

class ParagraphParser
{
    use HasChildNodes;
    use HasClasses;
    use HasAnchors;

    public function __construct(
        public readonly DOMElement $element,
        public readonly array $inherited = [],
    ) {}

    public function parse(): array
    {
        return [
            new Paragraph(
                $this->children(inherited: [...$this->inherited, ...$this->getStyles(), ...$this->getAnchor()]),
            ),
        ];
    }
}
