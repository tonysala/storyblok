<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Marks\Anchor;

/**
 * @property \DOMElement $element
 */
trait HasAnchors
{
    public function containsAnchor(): bool
    {
        return $this->element->hasAttribute('id');
    }

    public function getAnchor(): array
    {
        if ($this->containsAnchor()) {
            return [new Anchor($this->element->getAttribute('id'))];
        }

        return [];
    }
}
