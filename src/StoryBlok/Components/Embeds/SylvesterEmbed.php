<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class SylvesterEmbed extends Component
{
    public const NAME = 'sylvesterEmbed';

    public function __construct() {}

    public function toArray(): array
    {
        return [
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self();
    }
}
