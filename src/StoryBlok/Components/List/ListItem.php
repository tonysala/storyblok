<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\List;

use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Document;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Fields\Link\Target;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class ListItem extends Component
{
    public const NAME = 'SB015_2';

    public function __construct(
        public readonly Document $content,
        public readonly Link $link,
        public readonly ?Asset $image = null,
        public readonly string $title = '',
        public readonly Target $target = Target::SELF,
        public readonly string $linkText = '',
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'linkTo' => $this->link->serialise(),
            'linkText' => $this->linkText,
            'image' => $this->image?->serialise(),
            'target' => $this->target->value,
            'content' => $this->content->serialise(),
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            content: Document::deserialise($data['content']),
            link: Link::deserialise($data['link']),
            image: Asset::deserialise($data['image']),
            title: $data['title'],
            target: Target::tryFrom($data['target']),
            linkText: $data['linkText'],
        );
    }
}
