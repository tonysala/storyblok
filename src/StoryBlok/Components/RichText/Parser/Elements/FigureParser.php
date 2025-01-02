<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Image\AspectRatio;
use App\Services\StoryBlok\Components\Image\CaptionField;
use App\Services\StoryBlok\Components\Image\Image;
use App\Services\StoryBlok\Components\Image\Orientation;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Factories\AssetFactory;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Resources\Asset as ResourceAsset;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class FigureParser
{
    public function __construct(
        public readonly DOMElement $element,
    ) {
    }

    public function parse(): array
    {
        /** @var DOMXPath $xpath */
        $xpath = new DOMXPath($this->element->ownerDocument);

        /** @var ?DOMElement $imageElement */
        $imageElement = $xpath->query('//img', $this->element)->item(0);
        $caption = $xpath->evaluate('string(//figcaption)', $this->element);

        if ($imageElement) {
            $image = AssetFactory::fromHtmlElement($imageElement);
        } else {
            Log::warning('No image in figure element');

            return [];
        }

        $captionField = match ($caption) {
            $image->data['title'] => CaptionField::TITLE,
            $image->data['alt'] => CaptionField::ALT,
            $image->data['copyright'] => CaptionField::COPYRIGHT,
            default => $this->determineCaption($image, $caption),
        };

        $component = new Image(
            Asset::fromResource($image),
            $captionField,
            Orientation::LANDSCAPE,
            AspectRatio::WIDE,
        );

        return [new Blok([
            $component,
        ])];
    }

    protected function determineCaption(ResourceAsset &$image, mixed $caption): CaptionField
    {
        if (preg_match('/(\b(©|\(c\)|copyright|rights|licensed|photographer|credit|artist|source)\b)/i', $caption)) {
            if (empty($image->data['copyright'])) {
                $image = $image->update([
                    'copyright' => $caption,
                ]);
            }

            return CaptionField::COPYRIGHT;
        }

        if (empty($image->data['title'])) {
            $image = $image->update([
                'copyright' => $caption,
            ]);
        }

        return CaptionField::TITLE;
    }
}
