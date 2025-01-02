<?php

namespace App\Services\StoryBlok\Components\Templates;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Fields\MetaTags;

class PortalTemplate extends Component
{
    public const NAME = 'SB001_3';

    public function __construct(
        #[WithComponents] public readonly array $body = [],
        public readonly ?MetaTags $tags = new MetaTags(),
        public readonly ?Link $canonicalUrl = null,
        public readonly ?Robots $robots = Robots::ALL,
        public readonly bool $excludeFromNavigation = false,
        public readonly Header $header = Header::LIGHT,
        public readonly Footer $footer = Footer::DARK,
        public readonly Theme $theme = Theme::TWO,
    ) {}

    public function toArray(): array
    {
        return [
            'body' => $this->body,
            'excludeFromNav' => $this->excludeFromNavigation,
            'header' => $this->header,
            'footer' => $this->footer,
            'theme' => $this->theme,
            'canonical_url' => $this->canonicalUrl,
            'Metatags' => $this->tags,
            'Robots' => $this->robots,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            body: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['body']),
            tags: MetaTags::deserialise($data['Metatags']),
            canonicalUrl: isset($data['canonical_url']) ? Link::deserialise($data['canonical_url']) : null,
            robots: Robots::from($data['Robots']),
            excludeFromNavigation: $data['excludeFromNavigation'] ?? false,
            header: Header::from($data['header']),
            footer: Footer::from($data['footer']),
            theme: Theme::from($data['theme']),
        );
    }
}
