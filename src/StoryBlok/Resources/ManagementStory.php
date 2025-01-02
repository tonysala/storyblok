<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\ManagementApi;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use App\Services\StoryBlok\Spaces\Space;
use Throwable;

/**
 * @phpstan-type ManagementStoryData array{
 *     id: int,
 *     name: string,
 *     parent_id: int,
 *     group_id: string,
 *     alternates: array{
 *         id: int,
 *         name: string,
 *         slug: string,
 *         full_slug: string,
 *         created_at: string,
 *         published_at: string,
 *         uuid: string
 *     }[],
 *     created_at: string,
 *     deleted_at: string,
 *     sort_by_date: string,
 *     tag_list: string[],
 *     updated_at: string,
 *     published_at: string,
 *     uuid: string,
 *     is_folder: bool,
 *     content: object,
 *     published: bool,
 *     slug: string,
 *     path: string,
 *     full_slug: string,
 *     default_root: string,
 *     disable_fe_editor: bool,
 *     parent: object,
 *     is_startpage: bool,
 *     unpublished_changes: bool,
 *     meta_data: object,
 *     imported_at: string,
 *     preview_token: object{
 *         pinned: bool
 *     },
 *     breadcrumbs: array{
 *         first_published_at: string,
 *         last_author: object{
 *             last_author_id: int
 *         }
 *     }[],
 *     translated_slugs: array{
 *         localized_paths: array{
 *             path: string,
 *             position: int
 *         }[]
 *     }[],
 *     position: int,
 *     release_id: int,
 *     scheduled_dates: string,
 *     favourite_for_user_ids: int[]
 * }
 */
class ManagementStory implements Story, Creatable, Deletable
{
    public function __construct(
        /** @var ManagementStoryData $data */
        public readonly array $data,
        protected bool $deleted = false,
    ) {}

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getUri(): string
    {
        return 'https://app.storyblok.com/#/me/spaces/185167/stories/0/0/'.$this->data['id'];
    }

    public function getFullSlug(): string
    {
        return $this->data['full_slug'];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isPublished(): bool
    {
        return $this->data['published'];
    }

    public function hasUnpublishedChanges(): bool
    {
        return $this->data['unpublished_changes'];
    }

    public static function load(int $id): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->getStory($id);
    }

    public function refresh(): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->getStory($this->data['id']);
    }

    public static function create(array $data): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->createStory($data);
    }

    public function delete(): bool
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);
        try {
            $result = $api->deleteStory($this);
            $this->deleted = true;

            return $result;
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    public function publish(): bool
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);
        try {
            return $api->publishStory($this);
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    public function unpublish(): bool
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);
        try {
            return $api->unpublishStory($this);
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    public function __serialize(): array
    {
        return [$this->data['id'], CurrentSpace::get()->value];
    }

    public function __unserialize(array $data): void
    {
        [$id, $space] = $data;
        $from = CurrentSpace::get();
        CurrentSpace::change(Space::from($space));
        $this->__construct($this->load($id)->data);
        CurrentSpace::change($from);
    }
}
