<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\HeroBanner;

use App\Services\StoryBlok\Components\ComponentGroup;
use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Link\Link;
use InvalidArgumentException;

class TitleTwo implements ComponentGroup
{
    public function __construct(
        public readonly string $lineOne = '',
        public readonly string $lineTwo = '',
        public readonly Colour $lineOneColour = Colour::WHITE,
        public readonly Colour $lineTwoColour = Colour::WHITE,
        public readonly ?Colour $lineOneBackgroundColour = Colour::BLACK,
        public readonly ?Colour $lineTwoBackgroundColour = Colour::BLACK,
        public readonly bool $lineOneIncreaseFontSize = false,
        public readonly bool $lineTwoIncreaseFontSize = false,
        public readonly ?Link $lineOneLink = null,
        public readonly ?Link $lineTwoLink = null,
    ) {
        if (! in_array($this->lineOneColour, [Colour::BLACK, Colour::WHITE, Colour::NOT_SET])) {
            throw new InvalidArgumentException('Invalid line one colour');
        }
        if (! in_array($this->lineTwoColour, [Colour::BLACK, Colour::WHITE, Colour::NOT_SET])) {
            throw new InvalidArgumentException('Invalid line two colour');
        }
    }

    public function toArray(): array
    {
        return [
            'title_2' => $this->lineOne,
            'title_2_l2' => $this->lineTwo,
            'textColour_2' => $this->lineOneColour->withPrefix('--'),
            'textColour_2_l2' => $this->lineTwoColour->withPrefix('--'),
            'largerTitleSize_2' => $this->lineOneIncreaseFontSize,
            'largerTitleSize_2_l2' => $this->lineTwoIncreaseFontSize,
            'titleBackground_2' => $this->lineOneBackgroundColour?->withPrefix('--'),
            'titleBackground_2_l2' => $this->lineTwoBackgroundColour?->withPrefix('--'),
            'link_2' => $this->lineOneLink?->serialise(),
            'link_2_l2' => $this->lineTwoLink?->serialise(),
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            lineOne: $data['title_2'] ?? '',
            lineTwo: $data['title_2_l2'] ?? '',
            lineOneColour: Colour::from(str_replace('--', '', $data['textColour_2'] ?? '')),
            lineTwoColour: Colour::from(str_replace('--', '', $data['textColour_2_l2'] ?? '')),
            lineOneBackgroundColour: Colour::from(str_replace('--', '', $data['titleBackground_2'] ?? '')),
            lineTwoBackgroundColour: Colour::from(str_replace('--', '', $data['titleBackground_2_l2'] ?? '')),
            lineOneIncreaseFontSize: $data['largerTitleSize_2'] ?? false,
            lineTwoIncreaseFontSize: $data['largerTitleSize_2_l2'] ?? false,
            lineOneLink: isset($data['link_2']) ? Link::deserialise($data['link_2']) : null,
            lineTwoLink: isset($data['link_2_l2']) ? Link::deserialise($data['link_2_l2']) : null,
        );
    }
}
