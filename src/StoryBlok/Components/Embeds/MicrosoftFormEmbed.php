<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Slider;

class MicrosoftFormEmbed extends Component
{
    public const NAME = 'SB006_17';

    public function __construct(
        public readonly string $link,
        public readonly Slider $height,
    ) {}

    public function toArray(): array
    {
        return [
            'embed' => $this->link,
            'height' => $this->height,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            link: $data['embed'],
            height: new Slider($data['height']['value']),
        );
    }
}
