<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Models\Liferay\LiferayFile;
use App\Services\StoryBlok\Components\RichText\Features\Marks\Link as LinkMark;
use App\Services\StoryBlok\Factories\AssetFactory;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Fields\Link\LinkType;
use App\Services\StoryBlok\Fields\Link\Target;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use DOMElement;
use DOMNode;
use Throwable;

class AnchorParser
{
    use HasChildNodes;
    use HasAnchors;

    public const BUTTON_CLASSES = ['btn', 'inlineCTABlack'];

    public function __construct(
        public readonly DOMElement|DOMNode $element,
        public readonly array $inherited = [],
    ) {}

    public function parse(): array
    {
        $classes = explode(' ', $this->element->getAttribute('class'));
        if (array_intersect(self::BUTTON_CLASSES, $classes)) {
            $parser = new ButtonParser($this->element);

            return $parser->parse();
        }

        $marks = [
            ...$this->inherited,
            ...$this->getAnchor(),
        ];

        $target = Target::tryFrom($this->element->getAttribute('target') ?: '_self') ?? Target::SELF;
        $link = new LinkMark(
            location: new Link(
                url: $this->element->getAttribute('href'),
                linkType: LinkType::URL,
                target: $target,
            ),
            anchor: $this->element->getAttribute('id'),
        );

        if (Link::isInternal($link->location->url)) {
            $parts = parse_url($link->location->url);
            $path = $parts['path'] ?? '';
            if (str_starts_with($path, '/documents/')) {
                if (! Link::isCurrentSpace($link->location->url)) {
                    try {
                        CurrentSpace::switch();
                        $file = LiferayFile::fromPath($link->location->url);
                        $asset = AssetFactory::fromFile($file);
                        $link = new LinkMark(
                            location: new Link(
                                url: $asset->getUri(),
                                linkType: LinkType::URL,
                                target: $target,
                            ),
                            anchor: $this->element->getAttribute('id'),
                        );
                    } finally {
                        CurrentSpace::switch();
                    }
                } else {
                    try {
                        $file = LiferayFile::fromPath($link->location->url);
                        $asset = AssetFactory::fromFile($file);
                        $link = new LinkMark(
                            location: new Link(
                                url: $asset->getUri(),
                                id: (string)$asset->getId(),
                                linkType: LinkType::ASSET,
                                target: $target,
                            ),
                            anchor: $this->element->getAttribute('id'),
                        );
                    } catch (Throwable) {
                        // continue
                    }
                }
            }
        }

        return $this->children($this->element, [...$marks, $link]);
    }
}
