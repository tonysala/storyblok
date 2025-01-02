<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Accordion;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Document;

class AccordionItem extends Component
{
    public const NAME = 'accordionItem';

    public function __construct(
        public readonly string $title,
        public readonly Document $content,
        public readonly string $slug = '',
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title === '' ? '-' : $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            content: new Document($data['content']['content']),
            slug: $data['slug'] ?? '',
        );
    }
}
