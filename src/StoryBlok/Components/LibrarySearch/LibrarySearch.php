<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\LibrarySearch;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class LibrarySearch extends Component
{
    public const NAME = 'librarySearch';

    public function __construct(
        #[WithComponents] public readonly array $searchButtons = [],
        #[WithComponents] public readonly array $otherButtons = [],
    ) {}

    public function toArray(): array
    {
        return [
            'search_button_block' => $this->searchButtons,
            'other_button_blocks' => $this->otherButtons,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            searchButtons: array_map(
                fn(array $item) => ComponentFactory::deserialise($item),
                $data['search_button_block'],
            ),
            otherButtons: array_map(
                fn(array $item) => ComponentFactory::deserialise($item),
                $data['other_button_blocks'],
            ),
        );
    }
}
