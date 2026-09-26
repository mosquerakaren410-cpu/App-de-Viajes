<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TraceIdMiddleware
{
    public function handle(Request $request, Closure $next) {

        $traceId = Str::uuid()->toString();

        $request->attributes->set(
            'trace_id',
            $traceId
        );

        return $next($request);
    }
}
