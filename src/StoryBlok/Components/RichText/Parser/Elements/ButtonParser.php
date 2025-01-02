<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\GenericButton\GenericButton;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Fields\Link\Target;
use DOMElement;

readonly class ButtonParser
{
    public function __construct(
        public DOMElement $element,
    ) {}

    public function parse(): array
    {
        $target = $this->element->getAttribute('target');
        $link = Link::create(
            url: $this->element->getAttribute('href'),
            target: Target::tryFrom($target) ?? Target::SELF,
        );

        return [
            new Blok([
                new GenericButton(
                    link: $link,
                    linkText: $this->element->textContent,
                ),
            ]),
        ];
    }
}
