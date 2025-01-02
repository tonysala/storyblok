<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\NavigationMenu;

use App\Services\StoryBlok\Api\UsesContentApi;
use App\Services\StoryBlok\Components\SerializableComponent;
use App\Services\StoryBlok\Resources\Story;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class CtaNavigation extends Component
{
    use SerializableComponent;
    use UsesContentApi;

    public const NAME = 'SB005-2';

    public function __construct(
        public readonly ?Story $parent = null,
        public readonly NavigationType $type = NavigationType::ALL,
        public readonly bool $hideCurrent = true,
        public readonly bool $showAsGrid = true,
    ) {}

    public function toArray(): array
    {
        return [
            'parent' => $this->parent?->getData()['uuid'],
            'navigationType' => $this->type->value,
            'hideCurrent' => $this->hideCurrent,
            'showAsGrid' => $this->showAsGrid,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            parent: isset($data['parent']) ? self::getContentApi()->getStory($data['parent']) : null,
            type: NavigationType::from($data['navigationType']),
            hideCurrent: $data['hideCurrent'],
            showAsGrid: $data['showAsGrid'],
        );
    }
}
