<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class InstagramEmbed extends Component
{
    public const NAME = 'instagram_embed';

    public function __construct(
        public readonly string $link,
    ) {}

    public function toArray(): array
    {
        return [
            'embed' => $this->link,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            link: $data['embed'],
        );
    }
}