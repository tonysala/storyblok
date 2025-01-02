<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\List\UnorderedList;

class UnorderedListParser extends ListParser
{
    public function parse(): array
    {
        return [
            new UnorderedList($this->items()),
        ];
    }
}
