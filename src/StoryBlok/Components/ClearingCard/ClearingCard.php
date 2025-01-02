<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\ClearingCard;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Link\Link;

class ClearingCard extends Component
{
    public const NAME = 'clearingCard';

    public function __construct(
        public readonly string $title,
        public readonly Asset $image,
        public readonly Link $link,
        public readonly string $content = '',
        public readonly string $anchor = 'Read more',
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'image' => $this->image->serialise(),
            'link' => $this->link->serialise(),
            'content' => $this->content,
            'anchor' => $this->anchor,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            image: Asset::deserialise($data['image']),
            link: Link::deserialise($data['link']),
            content: $data['content'],
            anchor: $data['anchor'],
        );
    }
}
