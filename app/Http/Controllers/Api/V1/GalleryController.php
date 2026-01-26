<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\GalleryResource;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page.size' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = $request->integer('page.size', 15);

        $galleries = QueryBuilder::for(Gallery::class)
            ->with('school','category')
            ->allowedFilters([
                AllowedFilter::belongsTo('category'),
            ])
            ->paginate($perPage);

        return GalleryResource::collection($galleries);
    }
}
