<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Accordion\Accordion;
use App\Services\StoryBlok\Components\Accordion\AccordionItem;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Document;
use DOMElement;
use DOMXPath;

class CollapsibleItemParser
{
    use HasChildNodes;

    public readonly DOMElement $element;

    public function __construct(
        public readonly array $elements,
    ) {
        $this->element = $this->elements[0];
    }

    public function parse(): array
    {
        /** @var DOMXPath $xpath */
        $xpath = new DOMXPath($this->elements[0]->ownerDocument);

        $items = [];
        foreach ($this->elements as $node) {
            $title = $xpath->evaluate(
                'string(.//div[contains(concat(" ", @class, " "), " collapsible-item-title-link ")])',
                $node,
            );

            $items[] = new AccordionItem(
                title: $title,
                content: new Document(
                    $this->children(
                        $xpath->query('.//*[contains(@class, "collapsible-item-body")]', $node)->item(0),
                    ),
                ),
            );
        }

        return [
            new Blok([
                new Accordion(items: $items, colour: Colour::WHITE),
            ]),
        ];
    }
}
