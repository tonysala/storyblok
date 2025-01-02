<?php

namespace App\Services\StoryBlok\Api;

use InvalidArgumentException;
use Throwable;

class SlugConflictException extends InvalidArgumentException
{
    public function __construct(
        public readonly string $slug,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }
}
