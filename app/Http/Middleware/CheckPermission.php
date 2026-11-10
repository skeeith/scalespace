<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions, $requiredAll = false)
    {
        $requiredAll = filter_var(preg_replace('/\s+/', '', $requiredAll), FILTER_VALIDATE_BOOLEAN);

        if (! UserRepository::hasPermission(explode('|', $permissions), $requiredAll)) {
            return redirect('/error');
        }

        return $next($request);
    }
}
