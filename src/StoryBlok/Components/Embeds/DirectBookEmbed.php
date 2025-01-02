<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class DirectBookEmbed extends Component
{
    public const NAME = 'directBookEmbed';

    public function __construct(
        public readonly string $channelCode,
    ) {}

    public function toArray(): array
    {
        return [
            'channelCode' => $this->channelCode,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            channelCode: $data['channelCode'],
        );
    }
}
