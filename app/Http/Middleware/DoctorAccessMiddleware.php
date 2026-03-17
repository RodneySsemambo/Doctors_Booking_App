<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DoctorAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('DOCTOR MIDDLEWARE HIT', [
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'user_type' => Auth::check() ? Auth::user()->user_type : null,
            'url' => $request->fullUrl(),
        ]);

        if (!Auth::check()) {
            Log::warning('DOCTOR MIDDLEWARE: NOT AUTHENTICATED');
            return redirect()->route('login');
        }

        if (Auth::user()->user_type !== 'doctor') {
            Log::warning('DOCTOR MIDDLEWARE: WRONG ROLE', [
                'actual_role' => Auth::user()->user_type,
            ]);
            abort(403, 'unauthorized access');
        }

        return $next($request);
    }
}
