<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Liferay\Parsers\Exceptions\ControlledWidthComponentException;
use DOMElement;
use DOMXPath;

class ContainerParser
{
    use HasChildNodes;
    use HasClasses;

    public function __construct(
        public readonly DOMElement $element,
        public readonly array $inherited = [],
    ) {}

    public function parse(): array
    {
        $xpath = new DOMXPath($this->element->ownerDocument);
        try {
            if ($xpath->evaluate('contains(@class, "width-")', $this->element)) {
                throw new ControlledWidthComponentException('Contains width control class.');
            }

            if ($xpath->evaluate('starts-with(@id, "scoopit-container-")', $this->element)) {
                $parser = new ScoopItParser($this->element);

                return $parser->parse();
            }

            if ($xpath->evaluate('starts-with(@id, "Collapsible")', $this->element)) {
                if (! $xpath->evaluate('starts-with(@id, "Collapsible")', $this->element->previousElementSibling)) {
                    $items = [
                        $this->element,
                    ];

                    $next = $this->element->nextElementSibling;
                    while ($next?->nodeType === XML_ELEMENT_NODE) {
                        if ($xpath->evaluate('starts-with(@id, "Collapsible")', $next)) {
                            $items[] = $next;
                            $next = $next->nextElementSibling;
                        } else {
                            break;
                        }
                    }

                    return (new CollapsibleItemParser($items))->parse();
                } else {
                    return [];
                }
            }
        } catch (ControlledWidthComponentException) {
            //
        }

        return $this->children(inherited: [...$this->inherited]);
    }
}
