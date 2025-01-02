<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

interface Deletable
{
    public function delete(): bool;
}
