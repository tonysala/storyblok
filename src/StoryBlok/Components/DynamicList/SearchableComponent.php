<?php

namespace App\Services\StoryBlok\Components\DynamicList;

use App\Services\StoryBlok\Resources\Story;
use Laravel\Scout\Searchable;

trait SearchableComponent {
    use Searchable;
    use StoryComponentState;

    protected ?Story $story;

    public function getModel()
    {
        return $this;
    }

    public function getScoutKey(): string
    {
        return (string)$this->story->getId();
    }
}