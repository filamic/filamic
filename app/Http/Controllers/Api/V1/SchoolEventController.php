<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolEventResource;
use App\Models\SchoolEvent;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SchoolEventController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page.size' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = $request->integer('page.size', 15);

        $schoolEvents = QueryBuilder::for(SchoolEvent::class)
            ->allowedFilters(
                AllowedFilter::scope('upcoming'),
            )
            ->with('school')
            ->paginate($perPage);

        return SchoolEventResource::collection($schoolEvents);
    }
}
