<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\FlourishEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class FlourishParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $xpath = new DOMXPath($this->element->ownerDocument);

        $source = $xpath->evaluate("string(//div[@class='flourish-embed']/@data-url)");
        $path = parse_url($source, PHP_URL_PATH);

        if (preg_match('#/story/(?P<id>[0-9]+?)/embed#', $path, $matches)) {
            $id = $matches['id'];
        } else {
            $id = '';
            Log::warning('Could not find the ID from flourish embed.');
        }

        return [new Blok([new FlourishEmbed($id)])];
    }
}
