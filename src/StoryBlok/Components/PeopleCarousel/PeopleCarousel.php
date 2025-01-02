<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\PeopleCarousel;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class PeopleCarousel extends Component
{
    public const NAME = 'SB046';

    public function __construct(
        public readonly string $title = '',
        public readonly int $columns = 4,
        public readonly int $limit = 12,
        public readonly bool $showAsGrid = false,
        public readonly array $blocks = [],
        public readonly array $orgs = [],
        public readonly LogicalOperator $orgBool = LogicalOperator::NOT_SET,
        public readonly MatchType $orgType = MatchType::ANY,
        public readonly array $systems = [],
        public readonly LogicalOperator $sysBool = LogicalOperator::NOT_SET,
        public readonly MatchType $sysType = MatchType::ANY,
        public readonly array $roles = [],
        public readonly LogicalOperator $roleBool = LogicalOperator::NOT_SET,
        public readonly MatchType $roleType = MatchType::ANY,
        public readonly bool $searchBox = false,
        public readonly bool $paginationDots = false,
        public readonly array $keywords = [],
    ) {}

    public function toArray(): array
    {
        return [
            'limit' => (string)$this->limit,
            'title' => $this->title,
            'columns' => (string)$this->columns,
            'linkText' => $this->searchBox ? 'https://www.uea.ac.uk/search?facet=people' : '',
            'searchBox' => $this->searchBox,
            'showAsGrid' => $this->showAsGrid,
            'selectDept' => $this->orgs,
            'orgAndOr' => $this->orgBool,
            'orgAnyOrAll' => $this->orgType,
            'selectRole' => $this->roles,
            'roleAndOr' => $this->roleBool,
            'roleAnyOrAll' => $this->roleType,
            'selectSystems' => $this->systems,
            'sysAndOr' => $this->sysBool,
            'sysAnyOrAll' => $this->sysType,
            'selectKeywords' => $this->keywords,
            'show_pagination_dots' => $this->paginationDots,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            title: $data['title'],
            columns: $data['columns'] ? (int)$data['columns'] : 4,
            limit: $data['limit'] ? (int)$data['limit'] : 12,
            showAsGrid: $data['showAsGrid'],
            orgs: $data['selectDept'],
            orgBool: LogicalOperator::from($data['orgAndOr']),
            orgType: MatchType::from($data['orgAnyOrAll']),
            systems: $data['selectSystems'],
            sysBool: LogicalOperator::from($data['sysAndOr']),
            sysType: MatchType::from($data['sysAnyOrAll']),
            roles: $data['selectRole'],
            roleBool: LogicalOperator::from($data['roleAndOr']),
            roleType: MatchType::from($data['roleAnyOrAll']),
            searchBox: $data['searchBox'],
            paginationDots: $data['show_pagination_dots'],
            keywords: $data['selectKeywords'],
        );
    }
}
