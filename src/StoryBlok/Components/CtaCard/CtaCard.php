<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\CtaCard;

use App\Services\StoryBlok\Api\ContentApi;
use App\Services\StoryBlok\Api\ManagementApi;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Fields\Link\LinkType;
use App\Services\StoryBlok\Resources\ContentStory;
use App\Services\StoryBlok\Resources\Story;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CtaCard extends Component
{
    public const REUSABLE_CONTENT_SLUG = '__content/gs004-1/';

    public const NAME = 'ctaWithImage';

    public static array $components = [];

    protected string $reusableContent = '';

    public function __construct(
        public readonly string $title,
        public readonly Asset $image,
        public readonly Link $link,
        public readonly string $summary = '',
        public readonly DisplayType $display = DisplayType::STANDARD,
        public readonly string $anchor = 'Read more',
        string $reusableContent = '',
    ) {
        try {
            if ($reusableContent) {
                $this->reusableContent = $reusableContent;
            } else {
                $this->reusableContent = $this->exists($title, $link, $image)->getData()['uuid'];
                Log::debug('Found existing reusable content ['.$this->reusableContent.']');
            }
        } catch (Throwable $exception) {
            $this->reusableContent = $this->reuse()->getData()['uuid'];
            Log::debug('Created new reusable content ['.$this->reusableContent.']');
        }
    }

    /**
     * @return array<string, ContentStory>
     */
    public static function components(): array
    {
        if (app()->environment() !== 'production') {
            $slug = '__training/'.self::REUSABLE_CONTENT_SLUG;
        } else {
            $slug = self::REUSABLE_CONTENT_SLUG;
        }

        $page = 1;
        $continue = true;

        while ($continue) {
            /** @var ContentApi $api */
            $api = app(ContentApi::class);
            $response = $api->searchStories([
                'starts_with' => $slug,
                'sort_by' => 'created_at:desc',
                'per_page' => 100,
                'page' => $page,
            ]);

            $continue = $page < ceil($response->total / 100);

            foreach ($response->resources as $resource) {
                if (! array_key_exists($resource->getData()['uuid'], static::$components)) {
                    static::$components[$resource->getData()['uuid']] = $resource;
                } else {
                    $continue = false;
                    break;
                }
            }
            $page++;
        }

        return static::$components;
    }

    public static function exists(...$factors): ContentStory
    {
        $standardise = function (string $link) {
            if (Link::isInternal($link)) {
                $url = parse_url($link)['path'] ?? '';
            } else {
                $url = $link;
            }

            return $url;
        };

        /**
         * @var string $title
         * @var Link $link
         * @var Asset $image
         */
        [$title, $link, $image] = $factors;

        $matching = null;
        foreach (self::components() as $story) {
            if (! ($story->getData()['content']['image']['meta_data']['FID'] ?? false)) {
                continue;
            }

            $url = $story->getData()['content']['link']['linktype'] === LinkType::URL->value
                ? $standardise($story->getData()['content']['link']['url'])
                : $story->getData()['content']['link']['cached_url'];

            $against = [
                $story->getData()['name'],
                rtrim($url, '/'),
                $story->getData()['content']['image']['meta_data']['FID'],
            ];

            if ($against === [$title, $link->getUri(), $image->metaData['FID']]) {
                $matching = $story;
                break;
            }
        }

        if ($matching !== null) {
            return $matching;
        }

        throw new RuntimeException('No reusable content found.');
    }

    /**
     * @throws \Random\RandomException
     */
    protected function reuse(): Story
    {
        $slug = match (app()->environment()) {
            'production' => self::REUSABLE_CONTENT_SLUG.Str::slug($this->title),
            default => '__training/'.ltrim(self::REUSABLE_CONTENT_SLUG, '/').Str::slug($this->title),
        };

        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);
        $story = $api->createStory(
            content: [
                'content' => $this->toArray(),
                'name' => $this->title,
                'slug' => ltrim($slug, '/').'-'.bin2hex(random_bytes(5)),
                'is_startpage' => false,
            ],
            publish: true,
        );
        self::$components[$story->getData()['uuid']] = $story;

        return $story;
    }

    public function toArray(): array
    {
        if ($this->reusableContent) {
            return [
                'reusableContent' => $this->reusableContent,
                'component' => 'ctaWithImage',
            ];
        }

        return [
            'title' => $this->title,
            'link' => $this->link->serialise(),
            'image' => $this->image->serialise(),
            'anchor' => $this->anchor,
            'display' => $this->display->value,
            'card_copy' => $this->summary,
            'reusableContent' => '',
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            image: Asset::deserialise($data['image']),
            link: Link::deserialise($data['link']),
            summary: $data['card_copy'],
            display: DisplayType::tryFrom($data['display']),
            anchor: $data['anchor'],
            reusableContent: $data['reusableContent'],
        );
    }
}
