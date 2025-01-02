<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

use App\Models\AssociatedOrganization;
use App\Models\Person;
use Illuminate\Support\Facades\Log;
use JsonSerializable;
use Throwable;

class People implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public function __construct(
        public readonly array $people = []
    ) {
    }

    public function serialise(): mixed
    {
        return array_filter(array_map(function ($person) {
            try {
                /** @var Person $model */
                $model = Person::with([
                    'activeAssociatedOrganizations.jobRole',
                    'activeAssociatedOrganizations.employmentType',
                ])
                    ->where('email', $person)
                    ->firstOrFail();
            } catch (Throwable) {
                Log::warning('Person not found: '.$person);

                return null;
            }

            return [
                'label' => implode(' ', [$model->getAttribute('forename'), $model->getAttribute('surname')]),
                'value' => [
                    'img' => $model->getAttribute('profile_photo') ?? '',
                    'url' => $model->getAttribute('uea_pretty_url'),
                    'title' => $model->getAttribute('title'),
                    'user_id' => $model->getAttribute('uea_user_id'),
                    'organisations' => [
                        'data' => [
                            array_map(function (AssociatedOrganization $organization) {
                                return [
                                    'jobRole' => [
                                        'name' => $organization->getRelationValue('jobRole')?->getAttribute('name'),
                                    ],
                                    'organization' => [
                                        'name' => $organization->getRelationValue('organization')?->getAttribute('name'),
                                    ],
                                ];
                            }, $model->getRelationValue('activeAssociatedOrganizations')->all()),
                        ],
                    ],
                ],
            ];
        }, $this->people));
    }

    public static function deserialise(array $data): self
    {
        $ids = array_map(fn (array $person) => $person['value']['user_id'], $data);

        return new self(
            Person::where('uea_user_id', 'in', $ids)
                ->get()
                ->all(),
        );
    }
}
