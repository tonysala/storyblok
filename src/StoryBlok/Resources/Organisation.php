<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

class Organisation
{
    /**
     * @param  array{
     *   name: string,
     *   users: array{
     *     userid: string,
     *     email: string,
     *     username: ?string,
     *     real_email: string,
     *     avatar: ?string,
     *     id: int,
     *     organization: ?string,
     *     sign_in_count: int,
     *     created_at: string,
     *     firstname: string,
     *     lastname: string,
     *     org_role: string,
     *     last_sign_in_at: ?string,
     *     last_sign_in_ip: string,
     *     disabled: bool,
     *     partner_role: ?string,
     *     friendly_name: string,
     *     on_spaces: array{int},
     *     roles_on_spaces: array{
     *       int: array{
     *         string
     *       }
     *     }
     *   }[],
     *   spaces: array,
     *   settings: array,
     *   plan: string,
     *   billing_address: array,
     *   user_count: int,
     *   plan_level: int,
     *   max_spaces: int,
     *   max_collaborators: int,
     *   external_users: array,
     *   extended_external_users: array,
     *   track_statistics: bool,
     *   invitations: array{
     *     id: int,
     *     email: string,
     *     org_id: int,
     *     user_id: int,
     *     expires_at: string,
     *     org_role: string,
     *     inviter_id: int,
     *     registered: bool
     *   }[],
     *   sso_firstname: ?string,
     *   sso_lastname: ?string,
     *   sso_alt_email: ?string,
     *   strong_auth: ?bool,
     *   restricted_regions: array
     * }  $data
     */
    public function __construct(
        public array $data,
    ) {}

    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return array_map(fn(array $user) => new User($user), $this->data['users']);
    }

    public function getSpaces(): array
    {
        return $this->data['spaces'];
    }

    public function getSettings(): array
    {
        return $this->data['settings'];
    }

    public function getPlan(): string
    {
        return $this->data['plan'];
    }

    public function getBillingAddress(): array
    {
        return $this->data['billing_address'];
    }

    public function getUserCount(): int
    {
        return $this->data['user_count'];
    }

    public function getPlanLevel(): int
    {
        return $this->data['plan_level'];
    }

    public function getMaxSpaces(): int
    {
        return $this->data['max_spaces'];
    }

    public function getMaxCollaborators(): int
    {
        return $this->data['max_collaborators'];
    }

    public function getExternalUsers(): array
    {
        return $this->data['external_users'];
    }

    public function getExtendedExternalUsers(): array
    {
        return $this->data['extended_external_users'];
    }

    public function getTrackStatistics(): bool
    {
        return $this->data['track_statistics'];
    }

    public function getInvitations(): array
    {
        return $this->data['invitations'];
    }

    public function getSsoFirstname(): ?string
    {
        return $this->data['sso_firstname'];
    }

    public function getSsoLastname(): ?string
    {
        return $this->data['sso_lastname'];
    }

    public function getSsoAltEmail(): ?string
    {
        return $this->data['sso_alt_email'];
    }

    public function getStrongAuth(): ?bool
    {
        return $this->data['strong_auth'];
    }

    public function getRestrictedRegions(): array
    {
        return $this->data['restricted_regions'];
    }

    /**
     * @return array{
     *    name: string,
     *    users: array{
     *      userid: string,
     *      email: string,
     *      username: ?string,
     *      real_email: string,
     *      avatar: ?string,
     *      id: int,
     *      organization: ?string,
     *      sign_in_count: int,
     *      created_at: string,
     *      firstname: string,
     *      lastname: string,
     *      org_role: string,
     *      last_sign_in_at: ?string,
     *      last_sign_in_ip: string,
     *      disabled: bool,
     *      partner_role: ?string,
     *      friendly_name: string,
     *      on_spaces: array{int},
     *      roles_on_spaces: array{
     *        int: array{
     *          string
     *        }
     *      }
     *    }[],
     *    spaces: array,
     *    settings: array,
     *    plan: string,
     *    billing_address: array,
     *    user_count: int,
     *    plan_level: int,
     *    max_spaces: int,
     *    max_collaborators: int,
     *    external_users: array,
     *    extended_external_users: array,
     *    track_statistics: bool,
     *    invitations: array{
     *      id: int,
     *      email: string,
     *      org_id: int,
     *      user_id: int,
     *      expires_at: string,
     *      org_role: string,
     *      inviter_id: int,
     *      registered: bool
     *    }[],
     *    sso_firstname: ?string,
     *    sso_lastname: ?string,
     *    sso_alt_email: ?string,
     *    strong_auth: ?bool,
     *    restricted_regions: array
     *  }
     */
    public function getData(): array
    {
        return $this->data;
    }
}
