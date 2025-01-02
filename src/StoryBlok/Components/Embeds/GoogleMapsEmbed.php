<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class GoogleMapsEmbed extends Component
{
    public const NAME = 'google_maps_embed';

    public function __construct(
        public readonly string $id = '',
        public readonly string $embed = '',
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'embed' => $this->embed,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            id: $data['id'],
            embed: $data['embed'],
        );
    }
}
