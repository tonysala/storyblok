<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Video;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class Video extends Component
{
    public const NAME = 'video';

    public function __construct(
        public readonly string $link,
    ) {}

    public function toArray(): array
    {
        return [
            'youtube' => $this->link,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            link: $data['youtube'],
        );
    }
}
