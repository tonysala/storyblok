<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Api;

use App\Services\StoryBlok\Resources\ContentStory;
use App\Services\StoryBlok\Resources\DataSource;
use App\Services\StoryBlok\Resources\DataSourceEntry;
use App\Services\StoryBlok\Resources\PaginatedResource;
use App\Services\StoryBlok\Resources\Tag;
use App\Services\StoryBlok\Spaces\Space;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class ContentApi
{
    protected readonly array $auth;

    public function __construct(
        protected Client $client,
        protected array $config,
        protected string $visibility = 'draft',
        protected Space $space = Space::WWW,
    ) {
        $this->auth = [
            'token' => $this->config['spaces'][$this->space->value]['api_key'],
            'version' => $this->visibility,
        ];
    }

    /**
     * @throws RuntimeException When story is not found or the request fails
     */
    public function getStory(int|string $id): ContentStory
    {
        try {
            if (! is_numeric($id)) {
                $id = trim($id, '/').'/';
            }

            $response = $this->client->get('stories/'.$id);
            $data = json_decode($response->getBody()->getContents(), associative: true);

            if (empty($data['story'])) {
                throw new RuntimeException('Story '.$id.' not found.');
            }

            return new ContentStory($data['story']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    /** @return ContentStory[]|null[]
     * @throws Throwable
     */
    public function getStories(array $ids): iterable
    {
        $promises = [];
        foreach ($ids as $id) {
            $promises[] = $this->client->getAsync('stories/'.$id)->otherwise(fn($err) => $err);
        }

        return array_map(function (ResponseInterface|Throwable $response) {
            try {
                if (is_a($response, ResponseInterface::class)) {
                    $data = json_decode($response->getBody()->getContents(), associative: true);

                    return new ContentStory($data['story']);
                } else {
                    throw $response;
                }
            } catch (Throwable $err) {
                Log::info('Component could not be retrieved from Storyblok: '.$err->getMessage());

                return null;
            }
        }, Utils::unwrap($promises));
    }

    /**
     * Retrieves stories based on various filter criteria.
     *
     * @param  array{
     *     token?: string,
     *     cv?: number,
     *     version?: string,
     *     starts_with?: string,
     *     search_term?: string,
     *     sort_by?: string,
     *     per_page?: number,
     *     page?: number,
     *     by_slugs?: string,
     *     excluding_slugs?: string,
     *     published_at_gt?: string,
     *     published_at_lt?: string,
     *     first_published_at_gt?: string,
     *     first_published_at_lt?: string,
     *     in_workflow_stages?: number,
     *     content_type?: string,
     *     level?: number,
     *     resolve_relations?: string,
     *     excluding_ids?: string,
     *     by_uuids?: string,
     *     by_uuids_ordered?: string,
     *     with_tag?: string,
     *     is_startpage?: bool,
     *     resolve_links?: string,
     *     resolve_links_level?: number,
     *     from_release?: string,
     *     fallback_lang?: string,
     *     language?: string,
     *     filter_query?: string|array,
     *     excluding_fields?: string,
     *     resolve_assets?: number
     * }  $filters  Filters applied to the story retrieval.
     *
     * @return PaginatedResource<ContentStory> Returns a mixed response type based on the resolved properties and the
     *     requested data.
     * @throws RuntimeException If the request to the API fails.
     */
    public function searchStories(array $filters = []): PaginatedResource
    {
        try {
            $response = $this->client->get('stories', [
                'query' => [
                    'token' => $this->auth['token'],
                    'version' => $this->auth['version'],
                    ...$filters,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), associative: true);

            return new PaginatedResource(
                array_map(fn(array $story) => new ContentStory($story), $data['stories']),
                (int)($filters['page'] ?? 1),
                (int)$response->getHeaderLine('total'),
            );
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch stories: '.$exception->getMessage());
        }
    }

    /**
     * Retrieves stories based on various filter criteria.
     *
     * @param  array{
     *     token?: string,
     *     cv?: int,
     *     version?: string,
     *     starts_with?: string,
     *     search_term?: string,
     *     sort_by?: string,
     *     per_page?: int,
     *     page?: int,
     *     by_slugs?: string,
     *     excluding_slugs?: string,
     *     published_at_gt?: string,
     *     published_at_lt?: string,
     *     first_published_at_gt?: string,
     *     first_published_at_lt?: string,
     *     in_workflow_stages?: int,
     *     content_type?: string,
     *     level?: int,
     *     resolve_relations?: string,
     *     excluding_ids?: string,
     *     by_uuids?: string,
     *     by_uuids_ordered?: string,
     *     with_tag?: string,
     *     is_startpage?: bool,
     *     resolve_links?: string,
     *     resolve_links_level?: int,
     *     from_release?: string,
     *     fallback_lang?: string,
     *     language?: string,
     *     filter_query?: string|array,
     *     excluding_fields?: string,
     *     resolve_assets?: int,
     *     folder_only?: bool,
     *     story_only?: bool,
     * }  $filters  Filters applied to the story retrieval.
     *
     * @return PromiseInterface Returns a guzzle promise
     */
    public function searchStoriesAsync(array $filters = []): PromiseInterface
    {
        return $this->client->getAsync('stories', [
            'query' => [
                'token' => $this->auth['token'],
                'version' => $this->auth['version'],
                ...$filters,
            ],
        ]);
    }

    /**
     * @param  array{
     *     starts_with?: string,
     *     cv?: int,
     *     with_parent?: int,
     *     include_dates?: int,
     *     page?: int,
     *     per_page?: int,
     *     paginated?: bool
     * }  $filters  Filters applied to the link retrieval.
     *
     * @return PromiseInterface Returns a Guzzle promise
     */
    public function searchLinksAsync(array $filters = []): PromiseInterface
    {
        return $this->client->getAsync('links', [
            'query' => [
                'token' => $this->auth['token'],
                'version' => $this->auth['version'],
                ...$filters,
            ],
        ]);
    }

    public function getTag(string $name, bool $caseSensitive = false): Tag
    {
        try {
            $response = $this->client->get('tags');
            $data = json_decode($response->getBody()->getContents(), associative: true);

            if ($caseSensitive) {
                $matches = array_filter($data['tags'], fn(array $tag) => $tag['name'] === $name);
            } else {
                $matches = array_filter(
                    $data['tags'],
                    fn(array $tag) => strtolower($tag['name']) === strtolower($name),
                );
            }

            if (count($matches)) {
                return new Tag(array_values($matches)[0]);
            }

            throw new RuntimeException('Tag "'.$name.'" not found.');
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the tags: '.$exception->getMessage());
        }
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        try {
            $response = $this->client->get('tags');
            $data = json_decode($response->getBody()->getContents(), associative: true);

            return array_map(fn(array $tag) => new Tag($tag), $data['tags']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the tags: '.$exception->getMessage());
        }
    }

    /**
     * @return DataSource[]
     */
    public function getDataSources(): array
    {
        try {
            $response = $this->client->get('datasources');
            $data = json_decode($response->getBody()->getContents(), associative: true);

            return array_map(fn($datasource) => new DataSource($datasource), $data['datasources']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    public function getDataSource(int $id): DataSource
    {
        try {
            $response = $this->client->get('datasources/'.$id);
            $data = json_decode($response->getBody()->getContents(), associative: true);

            return new DataSource($data['datasource']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    /**
     * @param  string  $slug
     * @param  array{
     *     dimension?: string
     * }  $options
     *
     * @return DataSourceEntry[]
     */
    public function getDataSourceEntries(string $slug, array $options = []): array
    {
        try {
            $response = $this->client->get('datasource_entries', [
                'query' => [
                    'token' => $this->auth['token'],
                    'datasource' => $slug,
                    'per_page' => 200,
                    ...$options,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), associative: true);

            return array_map(fn(array $entry) => new DataSourceEntry($entry), $data['datasource_entries']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    /**
     * @throws RuntimeException When datasource is not found or the request fails
     */
    public function getDataSourceEntriesAsArray(string $datasource): array
    {
        try {
            $response = $this->client->get('datasource_entries', [
                'query' => [
                    'token' => $this->auth['token'],
                    'datasource' => $datasource,
                    'per_page' => 200,
                    'cv' => now()->unix(),
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), associative: true);

            $entries = [];
            foreach ($data['datasource_entries'] as $entry) {
                $entries[$entry['id'] ?? null] = $entry;
            }

            return $entries;
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    public static function version(string $version): self
    {
        if (! in_array($version, ['v1', 'v2'])) {
            throw new InvalidArgumentException('Invalid version');
        }

        return app()->make(self::class, [
            'version' => $version,
        ]);
    }

    /**
     * @param  array{
     *     visibility: string,
     *     version: string,
     * }  $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function with(array $config): self
    {
        return app()->make(self::class, [
            ...$config,
        ]);
    }
}
