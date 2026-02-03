<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAcademicPeriodIsReady
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isReady = cache()->remember('academic_period_ready', 3600, function () {
            $hasYear = SchoolYear::active()->exists();
            $hasTerm = SchoolTerm::active()->exists();

            return $hasYear && $hasTerm;
        });

        if (! $isReady) {
            return redirect()->route('setup_warning');
        }

        return $next($request);
    }
}
