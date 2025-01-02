<?php

namespace App\Services\StoryBlok\Components\PeopleCarousel;

enum LogicalOperator: string
{
    case AND = 'and';
    case OR = 'or';
    case NOT_SET = '';
}
