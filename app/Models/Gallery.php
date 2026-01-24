<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $school_id
 * @property int|null $gallery_category_id
 * @property string $name
 * @property string $slug
 * @property array<array-key, mixed> $images
 * @property \Illuminate\Support\Carbon $event_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read GalleryCategory|null $category
 * @property-read School|null $school
 *
 * @method static \Database\Factories\GalleryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereGalleryCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Gallery extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<\Database\Factories\GalleryFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function casts()
    {
        return [
            'images' => 'array',
            'event_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }
}
