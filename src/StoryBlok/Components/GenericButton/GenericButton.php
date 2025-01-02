<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\GenericButton;

use App\Services\StoryBlok\Enums\Colour;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class GenericButton extends Component
{
    public const NAME = 'SB027_2';

    public function __construct(
        public readonly Link $link,
        public readonly ?Asset $icon = null,
        public readonly string $linkText = 'Read more',
        public readonly bool $hideOnMobile = false,
        public readonly bool $center = true,
    ) {}

    public function toArray(): array
    {
        return [
            'icon' => $this->icon?->serialise(),
            'url' => $this->link->serialise(),
            'linkText' => $this->linkText,
            'hide_on_mobile' => $this->hideOnMobile,
            'width' => Width::AUTO,
            'colour' => '--'.Colour::BLACK->value,
            'type' => '__'.ButtonType::PRIMARY->value,
            'center' => $this->center,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            link: Link::deserialise($data['link']),
            icon: Asset::deserialise($data['icon']),
            linkText: $data['linkText'],
            hideOnMobile: $data['hideOnMobile'],
            center: $data['center'] ?? true,
        );
    }
}
