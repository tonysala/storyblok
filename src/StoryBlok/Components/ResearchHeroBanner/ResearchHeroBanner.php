<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\ResearchHeroBanner;

use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\People;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class ResearchHeroBanner extends Component
{
    public const NAME = 'SB012_4';

    public function __construct(
        public readonly Asset $image,
        public readonly Size $size = Size::DEFAULT,
        public readonly Colour $primaryColour = Colour::NOT_SET,
        public readonly People $people = new People([]),
        public readonly string $heading = '',
        public readonly string $imageCredit = '',
        public readonly Colour $colour = Colour::BLUE,
        public readonly HeadingPosition $headingPosition = HeadingPosition::ABOVE,
        public readonly bool $hideImageOnMobile = false,

    ) {}

    public function toArray(): array
    {
        return [
            'page_type' => 'Research',
            'heading' => $this->heading,
            'image' => $this->image->serialise(),
            'image_credit' => $this->imageCredit,
            'primary_colour' => $this->primaryColour->withPrefix('--'),
            'people' => $this->people->serialise(),
            'colour' => $this->colour->withPrefix('--'),
            'size' => $this->size->value,
            'heading_position' => $this->headingPosition->value,
            'hide_image_mobile' => $this->hideImageOnMobile,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            image: Asset::deserialise($data['image']),
            size: Size::tryFrom($data['size']),
            primaryColour: Colour::tryFrom(str_replace('--', '', $data['primary_colour'])),
            people: People::deserialise($data['people']),
            heading: $data['heading'],
            imageCredit: $data['image_credit'],
            colour: Colour::tryFrom(str_replace('--', '', $data['colour'])),
            headingPosition: HeadingPosition::tryFrom($data['heading_position']),
            hideImageOnMobile: $data['hide_image_on_mobile'],
        );
    }
}
