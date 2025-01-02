<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\NavigationMenu;

use App\Services\StoryBlok\Api\UsesContentApi;
use App\Services\StoryBlok\Components\SerializableComponent;
use App\Services\StoryBlok\Resources\Story;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class NavigationMenu extends Component
{
    use SerializableComponent;
    use UsesContentApi;

    public const NAME = 'navigationMenu';

    public function __construct(
        public readonly ?Story $parent = null,
        public readonly NavigationDepth $depth = NavigationDepth::ZERO,
    ) {}

    public function toArray(): array
    {
        return [
            'parent' => $this->parent?->getData()['uuid'],
            'depth' => $this->depth->value,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            parent: isset($data['order']) ? self::getContentApi()->getStory($data['order']) : null,
            depth: NavigationDepth::from($data['depth']),
        );
    }
}
