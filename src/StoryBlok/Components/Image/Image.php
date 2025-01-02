<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Image;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Asset;

class Image extends Component
{
    public const NAME = 'image';

    public function __construct(
        public readonly Asset $image,
        public readonly CaptionField $caption = CaptionField::NONE,
        public readonly Orientation $orientation = Orientation::LANDSCAPE,
        public readonly AspectRatio $aspectRatio = AspectRatio::NONE,
    ) {}

    public function toArray(): array
    {
        return [
            'image' => $this->image,
            'caption' => $this->caption->value,
            'orientation' => $this->orientation->value,
            'aspect_ratio' => $this->aspectRatio->value,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            image: Asset::deserialise($data['image']),
            caption: CaptionField::tryFrom($data['caption']),
            orientation: Orientation::tryFrom($data['orientation']),
            aspectRatio: AspectRatio::tryFrom($data['aspect_ratio']),
        );
    }
}
