<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class PublicSite
{
    /**
     * Redirects the former preview hosts (config app.legacy_hosts, e.g. new.onoh.org)
     * to the main site so shared links keep working, and renders dates in French.
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array(strtolower($request->getHost()), config('app.legacy_hosts'), true)) {
            $target = rtrim((string) config('app.url'), '/') . $request->getRequestUri();

            return redirect()->away($target, 301);
        }

        Carbon::setLocale('fr');

        return $next($request);
    }
}
