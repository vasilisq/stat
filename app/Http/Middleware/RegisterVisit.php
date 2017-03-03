<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Jobs\HandleVisitJob;

class RegisterVisit
{
    public function handle(Request $request, Closure $next)
    {
        // just put a job into queue
        dispatch(new HandleVisitJob($request));

        return $next($request);
    }
}