<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Api;

use App\Services\StoryBlok\Resources\Organisation;
use GuzzleHttp\Client;
use RuntimeException;
use Throwable;

class OAuthManagementApi
{
    public function __construct(
        protected readonly Client $client,
        protected readonly array $config,
    ) {}

    /**
     * Retrieves organisation data.
     *
     * @return Organisation
     * @throws RuntimeException If the request fails.
     */
    public function getOrganisation(): Organisation
    {
        try {
            $response = $this->client->get('orgs/me');
            $data = json_decode($response->getBody()->getContents(), true);

            return new Organisation($data['org']);
        } catch (Throwable $exception) {
            throw new RuntimeException('Failed to get organisation data: '.$exception->getMessage());
        }
    }
}
