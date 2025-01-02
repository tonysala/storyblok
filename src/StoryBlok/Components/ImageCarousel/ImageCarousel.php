<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\ImageCarousel;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class ImageCarousel extends Component
{
    public const NAME = 'imageCarousel';

    public function __construct(
        #[WithComponents] public readonly array $images,
        public readonly Height $size = Height::DEFAULT,
        public readonly bool $showThumbnails = false,
    ) {}

    public function toArray(): array
    {
        return [
            'carouselImages' => $this->images,
            'size' => $this->size->value,
            'showThumbnails' => $this->showThumbnails,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            images: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['carouselImages']),
            size: Height::tryFrom($data['size']),
            showThumbnails: $data['showThumbnails'],
        );
    }
}
