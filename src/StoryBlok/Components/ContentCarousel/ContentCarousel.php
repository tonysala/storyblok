<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\ContentCarousel;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class ContentCarousel extends Component
{
    public const NAME = 'contentCarousel';

    public function __construct(
        #[WithComponents] public readonly array $content,
//        public readonly Height $size = Height::DEFAULT,
//        public readonly bool $showThumbnails = false,
    ) {}

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            //            'size' => $this->size,
            //            'showThumbnails' => $this->showThumbnails,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            content: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['content']),
        );
    }
}
