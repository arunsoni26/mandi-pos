<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $module, $action): Response
    {
        $recordOwnerId = $request->route('user_id') ?? $request->input('user_id');

        if (!auth()->user()->hasPermission($module, $action, $recordOwnerId)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
