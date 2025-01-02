<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\PageHero;

use App\Liferay\Parsers\Templates\SectionHeader;
use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class PageHero extends Component
{
    public const NAME = 'SB012_3';

    public function __construct(
        public readonly Asset $image,
        public readonly string $heading,
        public readonly Colour $colour = Colour::BLUE,
        public readonly Size $size = Size::DEFAULT,
        public readonly Position $position = Position::HEADING_ABOVE_IMAGE,
        public readonly bool $hideOnMobile = false,
    ) {}

    public function toArray(): array
    {
        return [
            'image' => $this->image->id !== SectionHeader::PORTAL_DEFAULT_HERO_IMAGE_ID ? $this->image : null,
            'heading' => $this->heading,
            'colour' => $this->colour->withPrefix('--'),
            'size' => $this->size->value,
            'heading_position' => $this->position->value,
            'hide_image_mobile' => $this->hideOnMobile,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            image: Asset::deserialise($data['image']),
            heading: $data['heading'],
            colour: Colour::tryFrom(str_replace('--', '', $data['colour'])),
            size: Size::tryFrom($data['size']),
            position: Position::tryFrom($data['heading_position']),
            hideOnMobile: $data['hide_image_mobile'],
        );
    }
}
