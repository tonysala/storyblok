<?php

namespace App\Services\StoryBlok\Components\RichText\Features\Marks;

use App\Services\StoryBlok\Fields\SerializableField;
use JsonSerializable;

class Underline implements JsonSerializable
{
    use SerializableField;

    public function serialise(): array
    {
        return [
            'type' => 'underline',
        ];
    }
}
