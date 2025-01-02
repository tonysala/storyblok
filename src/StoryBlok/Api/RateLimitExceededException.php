<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Api;

use RuntimeException;

class RateLimitExceededException extends RuntimeException
{
}
