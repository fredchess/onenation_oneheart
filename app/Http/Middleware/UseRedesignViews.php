<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class UseRedesignViews
{
    /**
     * On a preview host (config app.preview_hosts), resolve views from
     * resources/views/{folder} first, so the same routes and controllers
     * render a redesigned public site.
     */
    public function handle(Request $request, Closure $next)
    {
        $folder = config('app.preview_hosts')[strtolower($request->getHost())] ?? null;

        if ($folder) {
            View::getFinder()->prependLocation(resource_path("views/{$folder}"));
            Carbon::setLocale('fr');
        }

        return $next($request);
    }
}
