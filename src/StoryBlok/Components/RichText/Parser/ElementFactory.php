<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser;

use DOMComment;
use DOMElement;
use DOMText;
use InvalidArgumentException;
use RuntimeException;

class ElementFactory
{
    public static function make(DOMElement|DOMText|DOMComment $element, $inherited = []): mixed
    {
        return match ($element->nodeName) {
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6' => new Elements\HeadingParser($element, $inherited),
            'p' => new Elements\ParagraphParser($element, $inherited),
            'strong', 'b' => new Elements\StrongParser($element, $inherited),
            '#text' => new Elements\TextParser($element, $inherited),
            'u' => new Elements\UnderlineTextParser($element, $inherited),
            'i', 'em' => new Elements\ItalicTextParser($element, $inherited),
            'a' => new Elements\AnchorParser($element, $inherited),
            'br' => new Elements\BreakParser($element),
            'hr' => new Elements\HorizontalRuleParser($element),
            'div',
            'li',
            'header',
            'footer',
            'center',
            'aside',
            'section',
            'main',
            'span',
            'label',
            'font' => new Elements\ContainerParser(
                $element,
                $inherited,
            ),
            'code' => new Elements\CodeParser($element, $inherited),
            'ol' => new Elements\OrderedListParser($element),
            'menu', 'ul' => new Elements\UnorderedListParser($element),
            'figure', => new Elements\FigureParser($element),
            'blockquote' => new Elements\BlockQuoteParser($element),
            'img' => new Elements\ImageParser($element, $inherited),
            'table' => new Elements\TableParser($element),
            'iframe' => new Elements\IframeParser($element),
            'script' => new Elements\ScriptParser($element),
            'sup' => new Elements\SuperscriptParser($element, $inherited),
            'sub' => new Elements\SubscriptParser($element, $inherited),
            'video' => new Elements\VideoParser($element),
            'ins' => new Elements\InsertedText($element, $inherited),
            'abbr' => new Elements\Abbreviation($element, $inherited),
            'figcaption',
            'noscript',
            'link',
            'style',
            'lt-toolbar',
            'meta',
            'form',
            '#comment' => throw new InvalidArgumentException('Element should not be parsed.'),
            'svg' => throw new InvalidArgumentException('Inline SVG are not supported.'),
            default => throw new RuntimeException('Node type not implemented: '.$element->nodeName),
        };
    }
}
