<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Paragraph\Paragraph;
use App\Services\StoryBlok\Components\RichText\Features\Text\Text;
use App\Services\StoryBlok\Components\RichText\Parser\ElementFactory;
use DOMText;
use InvalidArgumentException;

/**
 * @property \DOMElement $element
 */
trait HasChildNodes
{
    public const TEXT_WRAPPERS = [
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'strong',
        'span',
        'p',
        'b',
        'i',
        'em',
        'u',
        'font',
        'a',
        'blockquote',
        'cite',
        'code',
        'pre',
        'q',
        'small',
        'sub',
        'sup',
    ];

    public function children($parent = null, array $inherited = []): array
    {
        $parent ??= $this->element;
        /** @var \DOMElement $parent */
        $elements = [];

        // Required when parsing anchors
        if ($parent->childNodes->count() === 0) {
            return [new Text(text: ' ', marks: $inherited)];
        }

        $wrap = false;
        $nodes = $parent->childNodes;

        foreach ($nodes as $node) {
            if ($node instanceof DOMText && ! in_array($parent->nodeName, self::TEXT_WRAPPERS)) {
                $wrap = true;
            }
            /** @var \DOMElement|DOMText $node */
            try {
                $elements = [...$elements, ...ElementFactory::make($node, $inherited)->parse()];
            } catch (InvalidArgumentException) {
                continue;
            }
        }

        return $wrap ? [new Paragraph($elements)] : $elements;
    }
}
