<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlansMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //check if the user is an admin
        if (in_array(auth()->user()->role, [User::ROLE_ADMIN])) {
            return $next($request);
        }

        // check for user plan and duration
        $user = auth()->user();
        if ($user->plan_id && $user->plan_duration > now()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);

    }
}
