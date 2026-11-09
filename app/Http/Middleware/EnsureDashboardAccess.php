<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDashboardAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $username = (string) config('dashboard.username');
        $password = (string) config('dashboard.password');

        if ($username === '' || $password === '') {
            return response('Dashboard access is not configured.', 503);
        }

        $providedUsername = (string) $request->getUser();
        $providedPassword = (string) $request->getPassword();

        if (
            ! hash_equals($username, $providedUsername) ||
            ! hash_equals($password, $providedPassword)
        ) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="A.T.O.M. Dashboard"',
            ]);
        }

        return $next($request);
    }
}
