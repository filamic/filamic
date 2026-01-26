<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\Models\Gallery $resource
 *
 * @mixin \App\Models\Gallery
 */
class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'school' => $this->whenLoaded('school', fn () => [
                'id' => $this->school->getKey(),
                'name' => $this->school->name,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->getKey(),
                'name' => $this->category->name,
            ]),
            'name' => $this->name,
            'slug' => $this->slug,
            'images' => $this->formatted_images,
            'event_date' => $this->event_date->toISOString(),
        ];
    }
}
