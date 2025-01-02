<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class SpotifyEmbed extends Component
{
    public const NAME = 'spotifyEmbed';

    public function __construct(
        public readonly string $link,
        public readonly string $size = 'regular',
    ) {}

    public function toArray(): array
    {
        return [
            'embedLink' => $this->link,
            'size' => 'regular',
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            link: $data['embedLink'],
            size: $data['size'] ?? 'regular',
        );
    }
}
