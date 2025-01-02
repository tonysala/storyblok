<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\HeroBanner;

use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;
use InvalidArgumentException;

class HeroBanner extends Component
{
    public const NAME = 'gs012';

    public function __construct(
        public readonly Asset $image,
        public readonly Size $size = Size::DEFAULT,
        public readonly ?TitleOne $title = null,
        public readonly ?TitleTwo $titleTwo = null,
        public readonly ?SubText $subtext = null,
        public readonly Position $position = Position::BOTTOM,
        public readonly bool $bottomGradient = false,
        public readonly Colour $gradientColour = Colour::BLACK,
        public readonly bool $uppercase = false,
        public readonly bool $topGradient = false,
    ) {
        if (! in_array($this->gradientColour, [Colour::BLACK, Colour::WHITE])) {
            throw new InvalidArgumentException('Invalid gradient colour');
        }
    }

    public function toArray(): array
    {
        return [
            'component' => self::NAME,
            'image' => $this->image->serialise(),
            'size' => $this->size->value,
            'position' => $this->position->value,
            'topGradient' => $this->topGradient,
            'bottomGradient' => $this->bottomGradient,
            'gradientColour' => $this->gradientColour->withPrefix('--'),
            'uppercase' => $this->uppercase,
            ...$this->title?->toArray() ?? [],
            ...$this->titleTwo?->toArray() ?? [],
            ...$this->subtext?->toArray() ?? [],
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            image: Asset::deserialise($data['image']),
            size: Size::tryFrom($data['size']),
            title: TitleOne::deserialise($data),
            titleTwo: TitleTwo::deserialise($data),
            subtext: SubText::deserialise($data),
            position: Position::tryFrom($data['position']),
            bottomGradient: $data['bottomGradient'],
            gradientColour: Colour::tryFrom(str_replace('--', '', $data['gradientColour'])),
        );
    }
}
