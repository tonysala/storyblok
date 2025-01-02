<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Stats;

use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class StatsItem extends Component
{
    public const NAME = 'stat';

    public function __construct(
        public readonly string $title = '',
        public readonly string $intro = '',
        public readonly string $description = '',
        public readonly string $award = '',
        public readonly string $superscript = '',
        public readonly ?Link $link = null,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'intro' => $this->intro,
            'description' => $this->description,
            'source' => $this->link?->serialise(),
            'award' => $this->award,
            'sup' => $this->superscript,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            intro: $data['intro'],
            description: $data['description'],
            award: $data['award'],
            superscript: $data['sup'],
            link: Link::deserialise($data['source']),
        );
    }
}
