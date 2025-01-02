<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

class User
{
    /**
     * @param  array{
     *   userid: string,
     *   email: string,
     *   username: ?string,
     *   real_email: string,
     *   avatar: ?string,
     *   id: int,
     *   organization: ?string,
     *   sign_in_count: int,
     *   created_at: string,
     *   firstname: string,
     *   lastname: string,
     *   org_role: string,
     *   last_sign_in_at: ?string,
     *   last_sign_in_ip: ?string,
     *   disabled: bool,
     *   partner_role: ?string,
     *   friendly_name: string,
     *   on_spaces: array{int},
     *   roles_on_spaces: array{
     *     int: array{
     *       string
     *     }
     *   }
     * }  $data
     */
    public function __construct(
        public array $data,
    ) {}

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getUserId(): string
    {
        return $this->data['userid'];
    }

    public function getEmail(): string
    {
        return $this->data['email'];
    }

    public function getUsername(): ?string
    {
        return $this->data['username'];
    }

    public function getRealEmail(): string
    {
        return $this->data['real_email'];
    }

    public function getAvatar(): ?string
    {
        return $this->data['avatar'];
    }

    public function getOrganization(): ?string
    {
        return $this->data['organization'];
    }

    public function getSignInCount(): int
    {
        return $this->data['sign_in_count'];
    }

    public function getCreatedAt(): string
    {
        return $this->data['created_at'];
    }

    public function getFirstname(): string
    {
        return $this->data['firstname'];
    }

    public function getLastname(): string
    {
        return $this->data['lastname'];
    }

    public function getOrgRole(): string
    {
        return $this->data['org_role'];
    }

    public function getLastSignInAt(): ?string
    {
        return $this->data['last_sign_in_at'];
    }

    public function getLastSignInIp(): ?string
    {
        return $this->data['last_sign_in_ip'];
    }

    public function getDisabled(): bool
    {
        return $this->data['disabled'];
    }

    public function getPartnerRole(): ?string
    {
        return $this->data['partner_role'];
    }

    public function getFriendlyName(): string
    {
        return $this->data['friendly_name'];
    }

    public function getOnSpaces(): array
    {
        return $this->data['on_spaces'];
    }

    public function getRolesOnSpaces(): array
    {
        return $this->data['roles_on_spaces'];
    }

    /**
     * @return array{
     *   userid: string,
     *   email: string,
     *   username: ?string,
     *   real_email: string,
     *   avatar: ?string,
     *   id: int,
     *   organization: ?string,
     *   sign_in_count: int,
     *   created_at: string,
     *   firstname: string,
     *   lastname: string,
     *   org_role: string,
     *   last_sign_in_at: ?string,
     *   last_sign_in_ip: ?string,
     *   disabled: bool,
     *   partner_role: ?string,
     *   friendly_name: string,
     *   on_spaces: array{int},
     *   roles_on_spaces: array{
     *     int: array{
     *       string
     *     }
     *   }
     * }
     */
    public function getData(): array
    {
        return $this->data;
    }
}
