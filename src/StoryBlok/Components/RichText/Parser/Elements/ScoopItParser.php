<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\ScoopIt;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;
use DOMXPath;

class ScoopItParser
{
    public function __construct(
        public readonly DOMElement $element,
    ) {
    }

    public function parse(): array
    {
        $id = $this->element->getAttribute('id');
        preg_match('/^scoopit-container-(?P<topic>[0-9]+)$/', trim($id), $matches);

        $xpath = new DOMXPath($this->element->ownerDocument);
        foreach ($xpath->query('//script[not(@src)]') as $script) {
            if (preg_match('/nbPostPerPage\s?:\s?(?P<number>[0-9]+)/', $script->nodeValue, $scriptMatches)) {
                break;
            }
        }

        return [
            new Blok([
                new ScoopIt(
                    topicId: $matches['topic'] ?? '',
                    numberOfPosts: (int) ($scriptMatches['number'] ?? 24),
                ),
            ]),
        ];
    }
}
