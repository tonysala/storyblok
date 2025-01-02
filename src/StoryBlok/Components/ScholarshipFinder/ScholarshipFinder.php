<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\ScholarshipFinder;

use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class ScholarshipFinder extends Component
{
    public const NAME = 'scholarshipFinder';

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
