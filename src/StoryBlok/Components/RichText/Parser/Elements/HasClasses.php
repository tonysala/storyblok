<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Marks\Styled;
use DOMXPath;

trait HasClasses
{
    public function containsClass(string $class): bool
    {
        $xpath = new DOMXPath($this->element->ownerDocument);

        return $xpath->evaluate('contains(concat(" ", @class, " "), " '.$class.' ")]', $this->element);
    }

    public function getStyles(): array
    {
        $classes = [
            'intro-text' => 'intro',
            'text-centered' => 'center',
        ];

        $styles = [];
        foreach (explode(' ', $this->element->getAttribute('class')) as $name) {
            if (array_key_exists(trim($name), $classes)) {
                $styles[] = new Styled($classes[trim($name)]);
            }
        }

        return $styles;
    }
}
