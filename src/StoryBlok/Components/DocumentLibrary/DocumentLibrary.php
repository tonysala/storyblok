<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\DocumentLibrary;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Asset;

class DocumentLibrary extends Component
{
    public const NAME = 'SB066';

    public function __construct(
        public readonly array $assets = [],
    ) {}

    public function toArray(): array
    {
        return [
            'files' => $this->assets,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            assets: array_map(fn(array $item) => Asset::deserialise($item), $data['files']),
        );
    }
}
