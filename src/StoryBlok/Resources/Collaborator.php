<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\ManagementApi;

class Collaborator
{
    /**
     * @param  array{
     *   user: array{
     *     id: int,
     *     firstname: string,
     *     lastname: string,
     *     alt_email: ?string,
     *     disabled: bool,
     *     avatar: ?string,
     *     userid: string,
     *     friendly_name: string,
     *   },
     *   role: string,
     *   user_id: int,
     *   permissions: array,
     *   allowed_path: string,
     *   field_permissions: string,
     *   id: int,
     *   space_role_id: ?int,
     *   invitation: ?string,
     *   space_role_ids: array,
     *   space_id: int,
     * }  $data
     */
    public function __construct(
        public array $data,
    ) {}

    public static function load(int $id): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);
        return $api->getCollaborator($id);
    }

    public function getId(): int
    {
        return $this->data['id'];
    }

    /**
     * @return  array{
     *   user: array{
     *     id: int,
     *     firstname: string,
     *     lastname: string,
     *     alt_email: ?string,
     *     disabled: bool,
     *     avatar: ?string,
     *     userid: string,
     *     friendly_name: string,
     *   },
     *   role: string,
     *   user_id: int,
     *   permissions: array,
     *   allowed_path: string,
     *   field_permissions: string,
     *   id: int,
     *   space_role_id: ?int,
     *   invitation: ?string,
     *   space_role_ids: array,
     *   space_id: int,
     * }
     */
    public function getData(): array
    {
        return $this->data;
    }
}