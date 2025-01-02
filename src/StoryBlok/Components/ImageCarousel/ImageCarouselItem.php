<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\ImageCarousel;

use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Document;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class ImageCarouselItem extends Component
{
    public const NAME = 'SB013-2';

    public function __construct(
        public readonly string $title,
        public readonly Asset $image,
        public readonly Document $caption,
        public readonly bool $showCredit = false,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'image' => $this->image->serialise(),
            'caption' => $this->caption->serialise(),
            'showCredit' => $this->showCredit,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            image: Asset::deserialise($data['image']),
            caption: Document::deserialise($data['caption']),
            showCredit: $data['showCredit'],
        );
    }
}
