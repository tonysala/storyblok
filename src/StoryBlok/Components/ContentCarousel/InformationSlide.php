<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\ContentCarousel;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Link\Link;

class InformationSlide extends Component
{
    public const NAME = 'informationSlide';

    public function __construct(
        public readonly string $title,
        public readonly Asset $image,
        public readonly Link $link,
        public readonly string $abstract = '',
    ) {}

    public function toArray(): array
    {
        return [
            'Title' => $this->title,
            'Image' => $this->image->serialise(),
            'Link' => $this->link->serialise(),
            'Abstract' => $this->abstract,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['Title'],
            image: Asset::deserialise($data['Image']),
            link: Link::deserialise($data['Link']),
            abstract: $data['Abstract'],
        );
    }
}
