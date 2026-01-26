<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page.size' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = $request->integer('page.size', 15);

        $galleries = QueryBuilder::for(Gallery::class)
            ->allowedFilters(
                AllowedFilter::belongsTo('category'),
            )
            ->with('school', 'category')
            ->paginate($perPage);

        return GalleryResource::collection($galleries);
    }
}
