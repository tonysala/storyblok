<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class Echo360 extends Component
{
    public const NAME = 'SB006_20';

    public function __construct(
        public readonly string $id,
    ) {}

    public function toArray(): array
    {
        return [
            'video_id' => $this->id,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            id: $data['video_id'],
        );
    }
}
