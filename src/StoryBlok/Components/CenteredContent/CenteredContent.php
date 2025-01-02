<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\CenteredContent;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Enums\Colour;

class CenteredContent extends Component
{
    public const NAME = 'contentCentered';

    public function __construct(
        #[WithComponents] public readonly array $children,
        public readonly string $title = '',
        public readonly Colour $background = Colour::LIGHT_GREY,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'children' => $this->children,
            'background' => $this->background->withPrefix('background--'),
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            children: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['children']),
            title: $data['title'],
            background: Colour::tryFrom(str_replace('--', '', $data['background'])),
        );
    }
}
