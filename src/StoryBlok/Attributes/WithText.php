<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class WithText
{
    public function __construct() {}
}
