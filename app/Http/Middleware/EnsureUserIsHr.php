<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsHr
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->user_type === 'HR', 403);

        return $next($request);
    }
}
