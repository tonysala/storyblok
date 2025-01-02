<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\List\ListItem;
use App\Services\StoryBlok\Components\RichText\Parser\ElementFactory;
use DOMElement;
use DOMXPath;

/**
 * @property DOMElement $element
 */
abstract class ListParser
{
    public function __construct(
        public readonly DOMElement $element,
    ) {}

    public function items(): array
    {
        $items = [];
        $xpath = new DOMXPath($this->element->ownerDocument);
        foreach ($xpath->query('./*', $this->element) as $element) {
            /** @var DOMElement $element */
            $items[] = new ListItem(ElementFactory::make($element)->parse());
        }

        return $items;
    }

    abstract public function parse(): array;
}
