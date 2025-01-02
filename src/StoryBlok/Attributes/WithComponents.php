<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class WithComponents
{
    public function __construct() {}
}
