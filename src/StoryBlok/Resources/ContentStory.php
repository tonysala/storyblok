<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\ContentApi;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use App\Services\StoryBlok\Spaces\Space;

/**
 * @phpstan-type ContentStoryData array{
 *     name: string,
 *     created_at: string,
 *     published_at: string,
 *     id: int,
 *     uuid: string,
 *     content: object,
 *     slug: string,
 *     full_slug: string,
 *     sort_by_date: string,
 *     position: int,
 *     tag_list: string[],
 *     is_startpage: bool,
 *     parent_id: int,
 *     meta_data: object,
 *     group_id: string,
 *     first_published_at: string,
 *     release_id: int,
 *     lang: string,
 *     path: string,
 *     alternates: array{
 *         id: int,
 *         name: string,
 *         slug: string,
 *         full_slug: string,
 *         default_full_slug: string,
 *         created_at: string,
 *         published_at: string,
 *         uuid: string
 *     }[],
 *     translated_slugs: array{
 *         lang: string,
 *         slug: string,
 *         full_slug: string
 *     }[]
 * }
 */
class ContentStory implements Story
{
    public function __construct(
        /** @var ContentStoryData $data */
        public readonly array $data,
    ) {}

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function load(int $id): self
    {
        /** @var ContentApi $api */
        $api = app(ContentApi::class);

        return $api->getStory($id);
    }

    public function refresh(): self
    {
        /** @var ContentApi $api */
        $api = app(ContentApi::class);

        return $api->getStory($this->data['id']);
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
