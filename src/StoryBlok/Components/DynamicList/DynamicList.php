<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\DynamicList;

use App\Services\StoryBlok\Api\UsesContentApi;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Resources\Story;
use App\Services\StoryBlok\Resources\Tag;

class DynamicList extends Component
{
    use UsesContentApi;

    public const NAME = 'SB008_1';

    public function __construct(
        public readonly array $tags = [],
        public readonly string $title = '',
        public readonly string $anchorText = 'Read more',
        public readonly ?Story $section = null,
        public readonly DisplayType $display = DisplayType::DEFAULT,
        public readonly bool $showImages = true,
        public readonly bool $showSearch = true,
        public readonly bool $showAnchors = true,
        public readonly bool $showDescriptions = true,
    ) {}

    public function toArray(): array
    {
        return [
            'tags' => array_map(fn(Tag $tag) => $tag->toFormatted(), Tag::createMany($this->tags)),
            'Title' => $this->title,
            'display' => $this->display->value,
            'section' => $this->section?->getData()['uuid'],
            'anchorText' => $this->anchorText,
            'showImages' => $this->showImages,
            'showSearch' => $this->showSearch,
            'showAnchors' => $this->showAnchors,
            'showDescriptions' => $this->showDescriptions,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            tags: array_map(fn(array $tag) => $tag['value'], $data['tags']),
            title: $data['title'],
            anchorText: $data['anchorText'],
            section: isset($data['section']) ? self::getContentApi()->getStory($data['section']) : null,
            display: DisplayType::from($data['display']),
            showImages: $data['showImages'],
            showSearch: $data['showSearch'],
            showAnchors: $data['showAnchors'],
            showDescriptions: $data['showDescriptions'],
        );
    }
}
