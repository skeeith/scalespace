<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CheckShouldResetSecurityPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (auth()->user()->should_reset_security_pin === 1) {
            $token = Str::random(64);
            $email = auth()->user()->email;

            DB::table('security_pin_reset_tokens')->where('email', $email)->delete();

            DB::table('security_pin_reset_tokens')->insert([
                'email' => $email,
                'token' => Hash::make($token)
            ]);

            return redirect("/security-pin/reset/{$token}?email={$email}");
        }

        // $guards = empty($guards) ? [null] : $guards;

        // foreach ($guards as $guard) {
        //     if (Auth::guard($guard)->check() && Auth::guard($guard)->user()->should_reset_password === 1) {
        //         $token = Str::random(64);
        //         $email = Auth::guard($guard)->user()->email;

        //         DB::table('security_pin_reset_tokens')->where('email', $email)->delete();

        //         DB::table('security_pin_reset_tokens')->insert([
        //             'email' => $email,
        //             'token' => Hash::make($token)
        //         ]);

        //         return redirect("/security-pin/reset/{$token}?email={$email}");
        //     }
        // }

        return $next($request);
    }
}
