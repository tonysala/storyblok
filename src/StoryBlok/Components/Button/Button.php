<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Button;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Link\Link;

class Button extends Component
{
    public const NAME = 'button';

    public function __construct(
        public readonly Link $link,
        public readonly string $linkText,
    ) {}

    public function toArray(): array
    {
        return [
            'url' => $this->link,
            'linkText' => $this->linkText,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            link: Link::deserialise($data['url']),
            linkText: $data['linkText'],
        );
    }
}
