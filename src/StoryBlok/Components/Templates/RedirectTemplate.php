<?php

namespace App\Services\StoryBlok\Components\Templates;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\MetaTags;

class RedirectTemplate extends Component
{
    public const NAME = 'SB001_4';

    public function __construct(
        #[WithComponents] public readonly array $body = [],
        public readonly ?MetaTags $tags = new MetaTags(),
        public readonly bool $excludeFromNavigation = false,
    ) {}

    public function toArray(): array
    {
        return [
            'body' => $this->body,
            'excludeFromNav' => $this->excludeFromNavigation,
            'Metatags' => $this->tags,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            body: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['body']),
            tags: MetaTags::deserialise($data['Metatags']),
            excludeFromNavigation: $data['excludeFromNavigation'] ?? false,
        );
    }
}
