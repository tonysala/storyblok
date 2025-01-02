<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Heading\Heading;
use App\Services\StoryBlok\Components\RichText\Features\Heading\HeadingLevel;
use DOMElement;
use RuntimeException;

class HeadingParser
{
    use HasChildNodes;

    public function __construct(
        public readonly DOMElement $element,
        public readonly array $inherited = [],
    ) {}

    public function parse(): array
    {
        $level = match (strtolower($this->element->nodeName)) {
            'h1' => 1,
            'h2' => 2,
            'h3' => 3,
            'h4' => 4,
            'h5' => 5,
            'h6' => 6,
            default => throw new RuntimeException('Invalid heading.'),
        };

        return [
            new Heading(
                content: $this->children(inherited: $this->inherited),
                level: HeadingLevel::from($level),
            ),
        ];
    }
}
