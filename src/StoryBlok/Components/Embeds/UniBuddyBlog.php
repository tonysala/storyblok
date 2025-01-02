<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class UniBuddyBlog extends Component
{
    public const NAME = 'SB062';

    public function __construct(
        public readonly string $source,
    ) {}

    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            source: $data['source'],
        );
    }
}
