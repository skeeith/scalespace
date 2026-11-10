<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

class CheckAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles = null, $permissions = null, $requiredAll = false)
    {
        $roles       = preg_replace('/\s+/', '', $roles);
        $permissions = preg_replace('/\s+/', '', $permissions);
        $requiredAll = filter_var(preg_replace('/\s+/', '', $requiredAll), FILTER_VALIDATE_BOOLEAN);

        if (! UserRepository::hasAbility(explode('|', $roles), explode('|', $permissions), $requiredAll)) {
            return redirect('/error');
        }

        return $next($request);
    }
}
