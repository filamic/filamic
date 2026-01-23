<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\GalleryCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class GalleryCategory extends Model
{
    /** @use HasFactory<\Database\Factories\GalleryCategoryFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
