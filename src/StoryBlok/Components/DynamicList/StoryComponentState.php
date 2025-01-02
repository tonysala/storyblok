<?php

namespace App\Services\StoryBlok\Components\DynamicList;

use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Resources\Story;

trait StoryComponentState {
    protected ?Story $story;

    public static function deserialiseFromStory(Story $story): ComponentInterface
    {
        $object = static::deserialise($story->getData()['content']);
        /** @phpstan-ignore-next-line */
        $object->story = $story;
        return $object;
    }
}