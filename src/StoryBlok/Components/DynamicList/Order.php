<?php

namespace App\Services\StoryBlok\Components\DynamicList;

enum Order: string
{
    case ALPHABETICAL = '_by_name';
    case CREATED_DATE = '_by_created';
    case RELEVANCE = '';
}
