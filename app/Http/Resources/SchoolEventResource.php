<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\Models\SchoolEvent $resource
 *
 * @mixin \App\Models\SchoolEvent
 */
class SchoolEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'school' => $this->whenLoaded('school', fn () => [
                'id' => $this->school->getKey(),
                'name' => $this->school->name,
            ]),
            'name' => $this->name,
            'location' => $this->location,
            'start_date' => $this->start_date->toISOString(),
            'end_date' => $this->end_date->toISOString(),
        ];
    }
}
