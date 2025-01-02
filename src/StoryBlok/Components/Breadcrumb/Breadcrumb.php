<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Breadcrumb;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class Breadcrumb extends Component
{
    public const NAME = 'breadCrumb';

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
