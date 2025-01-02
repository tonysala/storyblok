<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\DynamicList;

use App\Services\StoryBlok\Api\UsesContentApi;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Categories;
use App\Services\StoryBlok\Fields\Category;
use App\Services\StoryBlok\Fields\Document;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Resources\Story;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use App\Services\StoryBlok\Spaces\Space;
use RuntimeException;

/**
 * @property ?Story $story
 */
class DynamicListItem extends Component
{
    use StoryComponentState;
    use SearchableComponent;
    use UsesContentApi;

    public const NAME = 'SB008_2';

    public function __construct(
        public readonly string $title = '',
        public readonly ?Asset $image = null,
        public readonly ?Document $content = null,
        public readonly ?Categories $categories = new Categories(),
        public readonly ?Link $url = null,
        public readonly ImageType $imageType = ImageType::IMAGE,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'image' => $this->image,
            'content' => $this->content,
            'datasource' => $this->categories,
            'url_override' => $this->url,
            'image_type' => $this->imageType,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            image: $data['image'] ? Asset::deserialise($data['image']) : null,
            content: Document::deserialise($data['content']),
            categories: $data['datasource'] ? Categories::deserialise($data['datasource']) : new Categories(),
            url: $data['url_override'] ? Link::deserialise($data['url_override']) : null,
            imageType: $data['image_type'] ? ImageType::from($data['image_type']) : ImageType::NOT_SET,
        );
    }

    public function searchableAs(): string
    {
        return match (CurrentSpace::get()) {
            Space::WWW => config('scout.algolia.indices.dynamic_list.www'),
            Space::PORTAL => config('scout.algolia.indices.dynamic_list.portal'),
            default => throw new RuntimeException('Current space must be set before indexing items.'),
        };
    }

    public function toSearchableArray(): array
    {
        $this->story || throw new RuntimeException('This object was not deserialised from a story.');

        $categories = array_reduce(
            $this->categories->categories,
            function (array $accumulator, Category $category) {
                if (! array_key_exists($category->dimension, $accumulator)) {
                    $accumulator[$category->dimension] = [];
                }
                $accumulator[$category->dimension][] = $category->label;
                return $accumulator;
            },
            [],
        );

        // TODO: remove unnecessary rich text features from the content.
//        $content = DocumentFactory::deserialise($this->content->content)->content;
//        $excerpt = Parser::only($content, Paragraph::class, Text::class);
//        dump($excerpt);

        return [
            'title' => $this->story->getData()['name'],
            'slug' => $this->story->getData()['full_slug'],
            'content' => $this->content ?? null,
            'image' => $this->image ?? null,
            'image_type' => $this->imageType ?? null,
            'url_override' => $this->url ?? null,
            'categories' => $categories,
            'datasource' => $this->categories?->datasource,
            'created_at' => $this->story->getData()['created_at'],
        ];
    }
}
