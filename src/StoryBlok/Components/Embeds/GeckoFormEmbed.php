<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class GeckoFormEmbed extends Component
{
    public const NAME = 'SB006-1';

    public function __construct(
        public readonly string $uuid,
    ) {}

    public function toArray(): array
    {
        return [
            'UUID' => $this->uuid,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            uuid: $data['UUID'],
        );
    }
}
