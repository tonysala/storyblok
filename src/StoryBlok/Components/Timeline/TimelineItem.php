<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Timeline;

use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Document;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class TimelineItem extends Component
{
    public const NAME = 'timelineItem';

    public function __construct(
        public readonly Asset $image,
        public readonly Document $body = new Document([]),
        public readonly string $heading = '',
        public readonly string $subHeading = '',
        public readonly Colour $colour = Colour::ORANGE,
    ) {}

    public function toArray(): array
    {
        return [
            'body' => $this->body->serialise(),
            'image' => $this->image->serialise(),
            'heading' => $this->heading,
            'subHeading' => $this->subHeading,
            'brandColour' => $this->colour->withPrefix('--'),
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            image: Asset::deserialise($data['image']),
            body: Document::deserialise($data['body']),
            heading: $data['heading'],
            subHeading: $data['subHeading'],
            colour: Colour::tryFrom(str_replace('--', '', $data['brandColour'])),
        );
    }
}
