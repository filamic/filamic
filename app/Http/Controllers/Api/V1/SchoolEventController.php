<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SchoolEvent;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolEventResource;

class SchoolEventController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolEvent::query();

        if ($request->string('filter')->lower() === 'upcoming') {
            $query->upcoming();
        }

        if ($request->filled('limit')) {
            $query->take(min((int) $request->limit, 100)); // Cap at 100
        }

        return SchoolEventResource::collection(
            $query->orderBy('starts_at')->get()
        );
    }
}
