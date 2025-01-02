<?php

namespace App\Services\StoryBlok\Components\DynamicList;

use App\Services\StoryBlok\Resources\Story;

interface StoryComponent {
    public static function deserialiseFromStory(Story $story);
}