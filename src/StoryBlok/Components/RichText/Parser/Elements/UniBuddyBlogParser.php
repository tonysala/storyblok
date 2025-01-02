<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\UniBuddyBlog;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class UniBuddyBlogParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        if (preg_match('#unibuddy.co/(.+?)/blog/topic#', $source)) {
            return [new Blok([new UniBuddyBlog($source)])];
        }

        return [];
    }
}
