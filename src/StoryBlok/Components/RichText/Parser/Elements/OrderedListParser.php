<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\List\OrderedList;

class OrderedListParser extends ListParser
{
    public function parse(): array
    {
        return [
            new OrderedList(
                items: $this->items(),
            ),
        ];
    }
}
