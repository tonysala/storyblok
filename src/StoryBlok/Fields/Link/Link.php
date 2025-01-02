<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields\Link;

use App\Models\Liferay\LiferayFile;
use App\Services\StoryBlok\Api\ContentApi;
use App\Services\StoryBlok\Api\UsesContentApi;
use App\Services\StoryBlok\Api\UsesManagementApi;
use App\Services\StoryBlok\Factories\AssetFactory;
use App\Services\StoryBlok\Fields\FieldInterface;
use App\Services\StoryBlok\Fields\SerializableField;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use Illuminate\Support\Facades\Log;
use JsonSerializable;
use RuntimeException;
use Throwable;

class Link implements FieldInterface, JsonSerializable
{
    use UsesManagementApi;
    use UsesContentApi;
    use SerializableField;

    public ?string $resolvedPath = null;

    public function __construct(
        public string $url,
        public ?string $id = null,
        public LinkType $linkType = LinkType::STORY,
        public Target $target = Target::SELF,
        public string $cachedUrl = '',
    ) {}

    public static function create(string $url, Target $target = Target::SELF): self
    {
        try {
            if (self::isInternal($url)) {
                $parts = parse_url($url);
                $path = $parts['path'] ?? '';
                if (str_starts_with($path, '/documents/')) {
                    if (! self::isCurrentSpace($url)) {
                        CurrentSpace::switch();
                        try {
                            $file = LiferayFile::fromPath($url);
                            $asset = AssetFactory::fromFile($file);
                            $link = new self(
                                url: $asset->getUri(),
                                linkType: LinkType::URL,
                                target: $target,
                                cachedUrl: $asset->getUri(),
                            );
                        } finally {
                            CurrentSpace::switch();
                        }
                        return $link;
                    } else {
                        $file = LiferayFile::fromPath($url);
                        $asset = AssetFactory::fromFile($file);

                        return new self(
                            url: $asset->getUri(),
                            id: (string)$asset->getId(),
                            linkType: LinkType::ASSET,
                            target: $target,
                            cachedUrl: $asset->getUri(),
                        );
                    }
                }

                return self::exists($url);
            } else {
                return new self(
                    url: $url,
                    linkType: LinkType::URL,
                    target: Target::BLANK,
                    cachedUrl: $url,
                );
            }
        } catch (Throwable) {
            return new self(
                url: $url,
                linkType: LinkType::URL,
                target: $target,
                cachedUrl: $url,
            );
        }
    }

    public static function isInternal(string $url): bool
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? null;

        if (! str_starts_with($parsed['scheme'] ?? 'http', 'http')) {
            return false;
        }

        if (! $host) {
            return true;
        }

        return str_contains($host, 'www.uea.ac.uk') || str_contains($host, 'my.uea.ac.uk');
    }

    public static function isCurrentSpace(string $url): bool
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? null;

        if (! str_starts_with($parsed['scheme'] ?? 'http', 'http')) {
            return false;
        }

        if (! $host) {
            return true;
        }

        return str_contains($host, parse_url(CurrentSpace::get()->getHost(), PHP_URL_HOST));
    }

    /**
     * @throws RuntimeException
     */
    public static function exists(string $url): self
    {
        if (parse_url($url, PHP_URL_FRAGMENT)) {
            throw new RuntimeException('Cannot use story link if the url contains a fragment.');
        }

        /** @var ContentApi $api */
        $api = app(ContentApi::class);
        $path = parse_url($url, PHP_URL_PATH);
        $story = $api->getStory($path);

        return new self(
            url: '/'.$story->data['full_slug'],
            id: $story->data['uuid'],
            linkType: LinkType::STORY,
            cachedUrl: '/'.$story->data['full_slug'],
        );
    }

    public function getUri(): string
    {
        if ($this->resolvedPath) {
            return $this->resolvedPath;
        }

        $relative = self::isInternal($this->url)
            && self::isCurrentSpace($this->url)
            && ! parse_url($this->url, PHP_URL_FRAGMENT);

        try {
            $path = match ($this->linkType) {
                LinkType::URL => $relative
                    ? parse_url($this->url, PHP_URL_PATH) ?? '/'
                    : $this->url,
                LinkType::ASSET => $this
                    ->getManagementApi()
                    ->getAsset((int)$this->id)
                    ->getUri(),
                LinkType::STORY => sprintf(
                    '/%s',
                    $this
                        ->getContentApi()
                        ->searchStories(['by_uuids' => $this->id])
                        ->resources[0]
                        ->getData()['full_slug'],
                ),
                LinkType::MAILTO => $this->url,
            };

            return $this->resolvedPath = rtrim($path, '/');
        } catch (Throwable $exception) {
            Log::warning('Could not resolve link path. '.$exception->getMessage());
            throw $exception;
        }
    }

    public function serialise(): array
    {
        return [
            'id' => $this->id ?? '',
            'url' => $this->url,
            'target' => $this->target,
            'linktype' => $this->linkType,
            'cached_url' => $this->cachedUrl ?: $this->url,
            'fieldtype' => 'multilink',
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            url: $data['url'],
            id: $data['id'],
            linkType: LinkType::from($data['linktype']),
            target: isset($data['target']) ? Target::from($data['target']) : Target::SELF,
            cachedUrl: $data['cached_url'],
        );
    }
}
