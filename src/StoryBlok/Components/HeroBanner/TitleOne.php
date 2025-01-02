<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\HeroBanner;

use App\Services\StoryBlok\Components\ComponentGroup;
use App\Services\StoryBlok\Enums\Colour;
use InvalidArgumentException;

class TitleOne implements ComponentGroup
{
    public function __construct(
        public readonly string $lineOne = '',
        public readonly string $lineTwo = '',
        public readonly Colour $lineOneColour = Colour::WHITE,
        public readonly Colour $lineTwoColour = Colour::WHITE,
        public readonly ?Colour $lineOneBackgroundColour = Colour::BLACK,
        public readonly ?Colour $lineTwoBackgroundColour = null,
        public readonly bool $lineOneIncreaseFontSize = true,
        public readonly bool $lineTwoIncreaseFontSize = false,
    ) {
        if (! in_array($this->lineOneColour, [Colour::BLACK, Colour::WHITE])) {
            throw new InvalidArgumentException('Invalid line one colour');
        }
        if (! in_array($this->lineTwoColour, [Colour::BLACK, Colour::WHITE])) {
            throw new InvalidArgumentException('Invalid line two colour');
        }
    }

    public function toArray(): array
    {
        return [
            'title' => $this->lineOne,
            'title_l2' => $this->lineTwo,
            'textColour' => $this->lineOneColour->withPrefix('--'),
            'textColour_l2' => $this->lineTwoColour->withPrefix('--'),
            'largerTitleSize' => $this->lineOneIncreaseFontSize,
            'largerTitleSize_l2' => $this->lineTwoIncreaseFontSize,
            'titleBackground' => $this->lineOneBackgroundColour->withPrefix('--'),
            'titleBackground_l2' => $this->lineTwoBackgroundColour?->withPrefix('--'),
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            lineOne: $data['title'] ?? '',
            lineTwo: $data['title_l2'] ?? '',
            lineOneColour: Colour::from(str_replace('--', '', $data['textColour'] ?? '')),
            lineTwoColour: Colour::from(str_replace('--', '', $data['textColour_l2'] ?? '')),
            lineOneBackgroundColour: Colour::from(str_replace('--', '', $data['titleBackground'] ?? '')),
            lineTwoBackgroundColour: Colour::tryFrom(str_replace('--', '', $data['titleBackground_l2'] ?? '')),
            lineOneIncreaseFontSize: $data['largerTitleSize'] ?? true,
            lineTwoIncreaseFontSize: $data['largerTitleSize_l2'] ?? false,
        );
    }
}
