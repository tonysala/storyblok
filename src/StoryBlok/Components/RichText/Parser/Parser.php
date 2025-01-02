<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser;

use App\Services\StoryBlok\Components\RichText\Features\Paragraph\Paragraph;
use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Components\RichText\Features\Text\Text;
use DOMComment;
use DOMDocument;
use DOMText;
use DOMXPath;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

class Parser
{
    public const DOCTYPE = '<!DOCTYPE html><meta charset="UTF-8">';

    public const NBSP = "\u{00A0}";

    public static function getXPath(string|DOMDocument $html): DOMXPath
    {
        $document = self::getDocument($html);
        return new DOMXPath($document);
    }

    public static function getDocument(string|DOMDocument $html): DOMDocument
    {
        if (is_string($html)) {
            libxml_use_internal_errors(true);
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->preserveWhiteSpace = false;
            $document->formatOutput = true;
            $document->normalize();
            if (! str_contains($html, '<body')) {
                $html = '<body>'.$html.'</body>';
            }
            $document->loadHTML(self::DOCTYPE.$html);
            libxml_clear_errors();
        } else {
            $document = $html;
        }

        return $document;
    }

    public static function parse(string|DOMDocument $html, bool $wrap = true): array
    {
        $document = self::getDocument($html);

        /** @var DOMXPath $xpath */
        $xpath = new DOMXPath($document);
        $elements = [];

        $nodes = $xpath->query('/html/body/node()');

        // Wrap the nodes if required
        if ($wrap && $nodes->item(0) instanceof DOMText && strlen(trim($nodes->item(0)->textContent))) {
            $paragraph = $document->createElement('p');
            foreach ($nodes as $node) {
                $paragraph->appendChild($node->cloneNode(true));
            }
            $document->getElementsByTagName('body')->item(0)->appendChild($paragraph);
            $nodes = [$paragraph];
        }

        /** @var \DOMElement|DOMText|DOMComment $node */
        foreach ($nodes as $node) {
            if ($node instanceof DOMComment) {
                continue;
            }

            if ($node instanceof DOMText) {
                $elements[] = $wrap ? new Paragraph([new Text($node->textContent)]) : new Text($node->textContent);
            } else {
                try {
                    foreach (ElementFactory::make($node)->parse() as $payload) {
                        $elements[] = $payload;
                    }
                } catch (InvalidArgumentException) {
                    // Skip element
                } catch (RuntimeException $exception) {
                    Log::error('Failed to parse HTML element - '.$exception->getMessage());
                }
            }
        }

        return $elements;
    }

    public static function only(array $elements, ...$components): array
    {
        return array_filter(
            $elements,
            function (RichTextFeature $element) use ($components) {
                return array_reduce(
                    $components,
                    fn($c, $component) => $c || $element instanceof $component,
                    false,
                );
            },
        );
    }

    public static function trimWhitespace(string $content): string
    {
        return trim(str_replace(self::NBSP, ' ', $content));
    }
}
