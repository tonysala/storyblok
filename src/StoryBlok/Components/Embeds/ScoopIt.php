<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class ScoopIt extends Component
{
    public const NAME = 'scoopitEmbed';

    public function __construct(
        public readonly string $topicId = '',
        public readonly int $numberOfPosts = 24,
    ) {}

    public function toArray(): array
    {
        return [
            'topicId' => $this->topicId,
            'numberPostPerPage' => (string)$this->numberOfPosts,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            topicId: $data['topicId'],
            numberOfPosts: $data['numberOfPostPerPage'] ?? 24,
        );
    }
}
