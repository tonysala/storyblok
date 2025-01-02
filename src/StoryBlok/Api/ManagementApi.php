<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Api;

use App\Models\Liferay\LiferayLayout;
use App\Services\Liferay\ImageService;
use App\Services\StoryBlok\Cache\InternalTagCache;
use App\Services\StoryBlok\NameNormaliser;
use App\Services\StoryBlok\Resources\Activity;
use App\Services\StoryBlok\Resources\Asset;
use App\Services\StoryBlok\Resources\AssetFolder;
use App\Services\StoryBlok\Resources\Collaborator;
use App\Services\StoryBlok\Resources\Component;
use App\Services\StoryBlok\Resources\Folder;
use App\Services\StoryBlok\Resources\InternalTag;
use App\Services\StoryBlok\Resources\ManagementStory;
use App\Services\StoryBlok\Resources\PaginatedResource;
use App\Services\StoryBlok\Resources\Preset;
use App\Services\StoryBlok\Resources\Story;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class ManagementApi
{
    public function __construct(
        protected Client $client,
        protected array $config,
        protected NameNormaliser $normaliser,
    ) {
    }

    public function getStory(int $id): ManagementStory
    {
        try {
            $response = $this->client->get('stories/'.$id);
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['story'])) {
                throw new RuntimeException('Story with id '.$id.' not found.');
            }

            return new ManagementStory($data['story']);
        } catch (RuntimeException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    /**
     * @return ManagementStory[]
     *
     * @throws Throwable
     */
    public function getStories(array $slugs): iterable
    {
        $promises = [];
        foreach ($slugs as $slug) {
            $promises[] = $this->client->getAsync('stories/', [
                'query' => [
                    'with_slug' => trim($slug, '/').'/',
                    'story_only' => true,
                ],
            ]);
        }

        return array_map(function (ResponseInterface $response) {
            $data = json_decode($response->getBody()->getContents(), associative: true);

            return new ManagementStory($data['stories'][0]);
        }, Utils::unwrap($promises));
    }

    /**
     * Searches for stories using various filters.
     *
     * @param  array{
     *     page?: int,
     *     contain_component?: string,
     *     text_search?: string,
     *     sort_by?: string,
     *     pinned?: bool,
     *     excluding_ids?: string,
     *     by_ids?: string,
     *     by_uuids?: string,
     *     with_tag?: string,
     *     folder_only?: bool,
     *     story_only?: bool,
     *     with_parent?: int,
     *     starts_with?: string,
     *     in_trash?: bool,
     *     search?: string,
     *     filter_query?: string|array,
     *     in_release?: int,
     *     is_published?: bool,
     *     by_slugs?: string,
     *     mine?: bool,
     *     excluding_slugs?: string,
     *     in_workflow_stages?: string,
     *     by_uuids_ordered?: string,
     *     with_slug?: string,
     *     with_summary?: bool,
     *     scheduled_at_gt?: string,
     *     scheduled_at_lt?: string,
     *     favourite?: bool,
     *     reference_search?: string
     * }  $filters  Optional. Filters applied to the story search.
     *
     * @return PaginatedResource<ManagementStory> Returns a paginated resource of stories.
     * @throws RuntimeException If the request fails.
     */
    public function searchStories(array $filters = []): PaginatedResource
    {
        try {
            $response = $this->client->get('stories', [
                'query' => $filters,
            ]);
            $data = json_decode($response->getBody()->getContents(), associative: true);

            return new PaginatedResource(
                array_map(fn(array $story) => new ManagementStory($story), $data['stories']),
                (int)($filters['page'] ?? 1),
                (int)$response->getHeaderLine('total'),
            );
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch stories: '.$exception->getMessage());
        }
    }

    public function createStory(array $content, bool $publish = false): ManagementStory
    {
        try {
            $data = [
                'story' => [
                    ...$content,
                    'parent_id' => $this->createParents($content['slug'], $content['is_startpage'] ?? true)->getId(),
                ],
                'publish' => $publish,
            ];
        } catch (GuzzleException $exception) {
            throw new RuntimeException(
                'Cannot create parent folders. - '.$content['slug'].PHP_EOL.$exception->getMessage(),
            );
        } catch (SlugConflictException $exception) {
            Log::warning('Trying to create a folder with slug '.$exception->slug.' but a story exists in it\'s place');
            throw $exception;
        }

        try {
            $folder = $this->getFolder($content['slug']);
            Log::debug('Story slug is the same as an existing folder, making story a startpage.');
            $data['story']['parent_id'] = $folder->getId();
            $data['story']['is_startpage'] = true;
        } catch (RuntimeException) {
            // Slug is available
        }

        try {
            $response = $this->client->post('stories', [
                'json' => $data,
            ]);

            $story = json_decode($response->getBody()->getContents(), true);

            return new ManagementStory($story['story']);
        } catch (RequestException $exception) {
            $data = json_decode($exception->getResponse()->getBody()->getContents(), associative: true);
            if (isset($data['slug'][0]) && preg_match('/Slug `(.*?)` already taken/', $data['slug'][0], $matches)) {
                throw new StoryCreationException(
                    'Story already exists with the same slug: '.$matches[1],
                );
            }
            throw new StoryCreationException($exception->getMessage(), previous: $exception);
        } catch (Throwable $exception) {
            throw new StoryCreationException('Cannot create story. '.$content['slug'].PHP_EOL.$exception->getMessage());
        }
    }

    public function updateStory(Story $story, array $updates, bool $publish = null): ManagementStory
    {
        try {
            $data = [
                'story' => [
                    ...$story->getData(),
                    ...$updates,
                ],
                ...(isset($publish) ? ['publish' => $publish] : []),
            ];

            $response = $this->client->put('stories/'.$story->getId(), [
                'json' => $data,
            ]);

            $story = json_decode($response->getBody()->getContents(), true);

            return new ManagementStory($story['story']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Cannot update story. - '.$story->getId().PHP_EOL.$exception->getMessage());
        }
    }

    public function moveStory(Story $story, string|Folder $folder): ManagementStory
    {
        if ($folder instanceof Folder) {
            $path = $folder->data['full_slug'];
        } else {
            $path = $folder;
        }

        $requiresRename = trim($story->getData()['full_slug'], '/') === trim($path, '/');

        // If the current story slug is the same as the intended target, we need to move the story to a temporary slug
        // otherwise it will fail when we try to create the folder
        if ($requiresRename) {
            $temporary = bin2hex(random_bytes(20));
            $slug = $story->getData()['slug'];
            $story = $this->updateStory($story, [
                'slug' => $temporary,
            ]);

            // Create the folder if needed
            if (! $folder instanceof Folder) {
                $folder = $this->createParents($path);
            }
        }

        // Set the parent folder
        $story = $this->updateStory($story, [
            'parent_id' => $folder instanceof Folder ? $folder->getId() : $this->createParents($path)->getId(),
        ]);

        // Change the slug back to the original
        if ($requiresRename) {
            $story = $this->updateStory($story, [
                'slug' => $slug,
                'is_startpage' => true,
            ]);
        }

        return $story;
    }

    public function publishStory(Story $story): bool
    {
        try {
            $response = $this->client->get('stories/'.$story->getId().'/publish');

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Cannot update story. - '.$story->getId().PHP_EOL.$exception->getMessage());
        }
    }

    public function unpublishStory(Story $story): bool
    {
        try {
            $response = $this->client->get('stories/'.$story->getId().'/unpublish');

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Cannot update story. - '.$story->getId().PHP_EOL.$exception->getMessage());
        }
    }

    public function getStoryVersions(string $id): array
    {
        try {
            $response = $this->client->get('stories/'.$id.'/versions');
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['versions'])) {
                throw new RuntimeException('Versions for story with id '.$id.' not found.');
            }

            return $data['versions'];
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    public function deleteStory(Story $story): bool
    {
        try {
            $response = $this->client->delete('stories/'.$story->getId());

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Cannot update story. - '.$story->getId().PHP_EOL.$exception->getMessage());
        }
    }

    public function isFolder(string $slug): bool
    {
        try {
            $this->getFolder($slug);
            return true;
        } catch (RuntimeException) {
            return false;
        }
    }

    public function getFolder(string $slug): Folder
    {
        try {
            $response = $this->client->get('stories/', [
                'query' => [
                    'with_slug' => $slug,
                    'folder_only' => true,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['stories'])) {
                throw new RuntimeException('Story with slug '.$slug.' not found.');
            }

            return new Folder($data['stories'][0]);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the story: '.$exception->getMessage());
        }
    }

    public function createFolder(string $slug, ?Folder $parent): Folder
    {
        try {
            return $this->getFolder($slug);
        } catch (RuntimeException) {
            $layout = LiferayLayout::query()
                ->where('friendlyURL', '/'.str_replace($this->config['prefix'], '', $slug))
                ->first();

            $data = [
                'story' => [
                    'name' => $layout ? $layout->getAttribute('name') : $this->normaliser->capitalise(basename($slug)),
                    'slug' => $slug,
                    'parent_id' => $parent->getId(),
                    'is_folder' => true,
                    ...($layout ? ['position' => $layout->getAttribute('priority')] : []),
                ],
            ];

            $response = $this->client->post('stories', [
                'json' => $data,
            ]);

            $story = json_decode($response->getBody()->getContents(), true);

            return new Folder($story['story']);
        }
    }

    public function createParents(string $path, bool $home = true): Folder
    {
        // Root folder
        $parent = new Folder(['id' => 0]);
        $level = -1;
        $pieces = explode('/', trim($path, '/'));

        if (! $home) {
            array_pop($pieces);
        }

        if (count($pieces) === 1 && $pieces[0] === '') {
            throw new InvalidArgumentException('Path contains no segments');
        }

        // Find the closest existing ancestor
        $reversed = array_reverse($pieces, preserve_keys: true);
        foreach ($reversed as $level => $piece) {
            $segment = trim(implode('/', array_slice($pieces, 0, $level)).'/'.$piece, '/');
            try {
                $parent = $this->getFolder($segment);
                if ($level === count($pieces) - 1) {
                    return $parent;
                }
            } catch (RuntimeException) {
                $level = $level === 0 ? -1 : $level;
                continue;
            }

            break;
        }

        $new = array_slice($pieces, $level + 1, preserve_keys: true);
        foreach ($new as $index => $piece) {
            $slug = implode('/', [...array_slice($pieces, 0, $index), $piece]);
            try {
                $parent = $this->createFolder($slug, $parent);
            } catch (RequestException $exception) {
                try {
                    $data = json_decode($exception->getResponse()->getBody()->getContents(), associative: true);
                } catch (Throwable) {
                    throw $exception;
                }

                if (isset($data['slug'][0]) && preg_match('/Slug `(.*?)` already taken/', $data['slug'][0], $matches)) {
                    throw new SlugConflictException(
                        slug: $matches[1],
                        message: 'Trying to create a folder but a story already exists with the same slug: '.$matches[1],
                    );
                }
            }
        }

        return $parent;
    }

    public function createAsset(string $file, array $attributes = [], ?int $parent = null): Asset
    {
        try {
            /** @var ImageService $imageService */
            $imageService = app(ImageService::class);
            $resource = $imageService->getImage($file);

            $response = $this->client->post('assets', [
                'json' => [
                    'internal_tag_ids' => [
                        InternalTagCache::getByName('Liferay')->getId(),
                    ],
                    'asset_folder_id' => $parent ?? CurrentSpace::get()->getAssetFolders()['root'],
                    ...$attributes,
                ],
            ]);
            $signature = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to upload image. Stage 1: '.$exception->getMessage());
        }

        try {
            $fields = array_map(function ($key) use ($signature) {
                return [
                    'name' => $key,
                    'contents' => $signature['fields'][$key],
                ];
            }, array_keys($signature['fields']));

            $client = new Client();
            $client->post($signature['post_url'], [
                'multipart' => [
                    ...$fields,
                    [
                        'name' => 'file',
                        'contents' => $resource,
                        'filename' => basename($attributes['name']),
                    ],
                ],
                'headers' => [
                    'Content-Length' => strlen($file),
                ],
            ]);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to upload image. Stage 2: '.$exception->getMessage());
        }

        try {
            $response = $this->client->get('assets/'.$signature['id'].'/finish_upload');
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data)) {
                throw new RuntimeException('No asset found.');
            }

            return $this->getAsset($data['id']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to upload image. Stage 3: '.$exception->getMessage());
        }
    }

    public function updateAsset(int $id, array $attributes): Asset
    {
        try {
            $response = $this->client->put('assets/'.$id, [
                'json' => $attributes,
            ]);
            if ($response->getStatusCode() === 204) {
                return $this->getAsset($id);
            } else {
                throw new RuntimeException('Failed to update image.');
            }
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to update image. '.$exception->getMessage());
        }
    }

    public function deleteAsset(Asset $asset): bool
    {
        try {
            $response = $this->client->delete('assets/'.$asset->getId());

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Cannot delete asset. - '.$asset->getId().PHP_EOL.$exception->getMessage());
        }
    }

    public function getAsset(int $id): Asset
    {
        try {
            $response = $this->client->get('assets/'.$id);
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data)) {
                throw new RuntimeException('No asset found.');
            }

            return new Asset($data);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to get asset: '.$exception->getMessage());
        }
    }

    /**
     * @param  AssetFolder  $parent
     * @param  string  $search
     * @param  int  $page
     *
     * @return PaginatedResource<Asset>
     */
    public function getAssets(AssetFolder|int $parent, string $search = '', int $page = 1): PaginatedResource
    {
        $parent = $parent instanceof AssetFolder ? $parent->getId() : $parent;

        try {
            return $this->searchAssets([
                'sort_by' => 'created_at:desc',
                'in_folder' => $parent,
                'search' => $search,
                'page' => $page,
                'per_page' => 100,
            ]);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to get assets: '.$exception->getMessage());
        }
    }

    /**
     * Searches for assets based on various filters and returns a paginated resource containing assets.
     *
     * @param  array{
     *     page?: int,
     *     in_folder?: number,
     *     sort_by?: string,
     *     is_private?: bool,
     *     search?: string,
     *     by_alt?: string,
     *     by_copyright?: string,
     *     by_title?: string
     * }  $filters  Filters to apply when searching for assets.
     *
     * @return PaginatedResource<Asset> Paginated resource containing assets.
     * @throws RuntimeException If the request to fetch assets fails.
     */
    public function searchAssets(array $filters): PaginatedResource
    {
        $page = $filters['page'] ?? 1;

        try {
            $response = $this->client->get('assets/', [
                'query' => $filters,
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            $assets = array_map(fn($asset) => new Asset($asset), $data['assets']);

            return new PaginatedResource($assets, $page, (int)$response->getHeaderLine('total'));
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to get assets: '.$exception->getMessage());
        }
    }

    public function fromLiferayAssetFolder(array $folders, ?int $parent = null): AssetFolder
    {
        $all = $this->getAssetFolders();
        if ($parent) {
            $root = $this->getAssetFolder($parent);
        } else {
            /** @phpstan-ignore-next-line */
            $root = new AssetFolder(['id' => CurrentSpace::get()->getAssetFolders()['root']]);
        }

        try {
            foreach ($folders as $level => $folder) {
                $found = array_filter($all, function (AssetFolder $f) use ($root, $folder) {
                    return $f->data['parent_id'] === $root->getId() && $f->getName() === $folder->getAttribute('name');
                });
                $root = count($found) ? reset($found) : throw new RuntimeException();
            }
        } catch (RuntimeException) {
            foreach (array_slice($folders, $level, preserve_keys: true) as $folder) {
                $root = $this->createAssetFolder($folder->getAttribute('name'), $root->getId());
            }
        }

        return $root;
    }

    public function getAssetFolder(int $id): AssetFolder
    {
        try {
            $response = $this->client->get('asset_folders/'.$id);
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['asset_folder'])) {
                throw new RuntimeException('No asset folders found.');
            }

            return new AssetFolder($data['asset_folder']);
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to get asset folders: '.$exception->getMessage());
        }
    }

    /**
     * @return AssetFolder[]
     */
    public function getAssetFolders(): array
    {
        try {
            $response = $this->client->get('asset_folders/');
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['asset_folders'])) {
                throw new RuntimeException('No asset folders found.');
            }

            return array_map(fn($folder) => new AssetFolder($folder), $data['asset_folders']);
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to get asset folders: '.$exception->getMessage());
        }
    }

    public function createAssetFolder(string $name, int $parentId): AssetFolder
    {
        $data = [
            'asset_folder' => [
                'name' => $name,
                'parent_id' => $parentId,
            ],
        ];

        $response = $this->client->post('asset_folders/', [
            'json' => $data,
        ]);

        $folder = json_decode($response->getBody()->getContents(), true);

        return new AssetFolder($folder['asset_folder']);
    }

    public function updateAssetFolder(int $id, array $attributes): AssetFolder
    {
        try {
            $response = $this->client->put('asset_folders/'.$id, [
                'json' => $attributes,
            ]);
            if ($response->getStatusCode() === 204) {
                return $this->getAssetFolder($id);
            } else {
                throw new RuntimeException('Failed to update asset folder.');
            }
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to update asset folder. '.$exception->getMessage());
        }
    }

    public function deleteAssetFolder(AssetFolder $folder): bool
    {
        try {
            $response = $this->client->delete('asset_folders/'.$folder->getId());

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (GuzzleException $exception) {
            throw new RuntimeException(
                'Cannot delete asset folder. - '.$folder->getId().PHP_EOL.$exception->getMessage(),
            );
        }
    }

    /**
     * Searches for internal tags based on various filters and returns an array of InternalTag objects.
     *
     * @param  array{
     *     search?: string,
     *     by_object_type?: string,
     *     page?: int
     * }  $filters  Filters to apply when searching for internal tags.
     *
     * @return PaginatedResource<\App\Services\StoryBlok\Resources\InternalTag>
     * @throws RuntimeException If the request fails.
     */
    public function getInternalTags(array $filters = []): PaginatedResource
    {
        try {
            $response = $this->client->get('internal_tags', [
                'query' => [
                    'page' => 1,
                    ...$filters,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            return new PaginatedResource(
                array_map(fn($tag) => new InternalTag($tag), $data['internal_tags']),
                (int)($filters['page'] ?? 1),
                (int)$response->getHeaderLine('total'),
            );
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to get internal tags: '.$exception->getMessage());
        }
    }

    public function getComponent(string $id): Component
    {
        try {
            $response = $this->client->get('components/'.$id);
            $data = json_decode($response->getBody()->getContents(), associative: true);

            if (empty($data['component'])) {
                throw new RuntimeException('Component '.$id.' not found.');
            }

            return new Component($data['component']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the component: '.$exception->getMessage());
        }
    }

    public function getPreset(string $id): Preset
    {
        try {
            $response = $this->client->get('presets/'.$id);
            $data = json_decode($response->getBody()->getContents(), true);

            return new Preset($data);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the preset: '.$exception->getMessage());
        }
    }

    public function getPresets(): array
    {
        try {
            $response = $this->client->get('presets');
            $data = json_decode($response->getBody()->getContents(), true);

            return array_map(fn(array $data) => new Preset($data), $data['presets']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the presets: '.$exception->getMessage());
        }
    }

    /**
     * Returns a paginated list of collaborators
     *
     * @param  array{
     *   page?: int,
     *   per_page?: int
     * }  $filters  Filters to apply when searching for collaborators.
     *
     * @return PaginatedResource<\App\Services\StoryBlok\Resources\Collaborator>
     * @throws RuntimeException If the request fails.
     */
    public function getCollaborators(array $filters = []): PaginatedResource
    {
        try {
            $response = $this->client->get('collaborators', [
                'query' => [
                    'page' => 1,
                    'per_page' => 100,
                    ...$filters,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            return new PaginatedResource(
                array_map(fn($collaborator) => new Collaborator($collaborator), $data['collaborators']),
                (int)($filters['page'] ?? 1),
                (int)$response->getHeaderLine('total'),
            );
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to get collaborators: '.$exception->getMessage());
        }
    }

    /**
     * Returns a collaborator
     *
     * @return \App\Services\StoryBlok\Resources\Collaborator
     * @throws RuntimeException If the request fails.
     */
    public function getCollaborator(int $id): Collaborator
    {
        try {
            $response = $this->client->get('collaborators/'.$id);
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['collaborator'])) {
                throw new RuntimeException('No collaborator found.');
            }

            return new Collaborator($data['collaborator']);
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to get collaborator: '.$exception->getMessage());
        }
    }

    public function deleteCollaborator(int $id): bool
    {
        try {
            $response = $this->client->delete('collaborators/'.$id);

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to disable collaborator: '.$exception->getMessage());
        }
    }

    /**
     * Retrieves a single activity with a specific numeric ID.
     *
     * @param  int  $activityId  The numeric ID of the activity.
     *
     * @return \App\Services\StoryBlok\Resources\Activity
     * @throws RuntimeException If the request fails.
     */
    public function getActivity(int $activityId): Activity
    {
        try {
            $response = $this->client->get('activities/'.$activityId);
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['activity'])) {
                throw new RuntimeException('Activity not found.');
            }

            return new Activity($data['activity']);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('Failed to fetch the activity: '.$exception->getMessage());
        }
    }

    /**
     * Retrieves activities with optional filters.
     *
     * @param  array{
     *     created_at_gte?: string,
     *     created_at_lte?: string,
     *     by_owner_ids?: int|string,
     *     types?: string,
     *     page?: int,
     *     per_page?: int,
     * }  $filters  Filters to apply when searching for activities.
     *
     * @return PaginatedResource<\App\Services\StoryBlok\Resources\Activity>
     * @throws RuntimeException If the request fails.
     */
    public function getActivities(array $filters = []): PaginatedResource
    {
        try {
            $response = $this->client->get('activities', [
                'query' => [
                    'page' => 1,
                    'per_page' => 100,
                    ...$filters,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            return new PaginatedResource(
                array_map(fn($activity) => new Activity($activity), $data['activities'] ?? []),
                $filters['page'] ?? 1,
                (int)$response->getHeaderLine('total'),
            );
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to get activities: '.$exception->getMessage());
        }
    }
}
