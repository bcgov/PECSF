<?php

// namespace Binafy\LaravelUserMonitoring\Middlewares;
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Binafy\LaravelUserMonitoring\Utills\Detector;
use Binafy\LaravelUserMonitoring\Utills\UserUtils;

class VisitMonitoringMiddleware
{
    /**
     * Handle monitor visiting.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (config('user-monitoring.visit_monitoring.turn_on', false) === false) {
            return $next($request);
        }
        if (config('user-monitoring.visit_monitoring.ajax_requests', false) === false && $request->ajax()) {
            return $next($request);
        }

        $detector = new Detector();
        $guard = config('user-monitoring.user.guard', 'web');
        $exceptPages = config('user-monitoring.visit_monitoring.except_pages', []);

        // Exclude non GET method for statistic
        if ($request->method() == 'GET') {
            if (empty($exceptPages) || !$this->checkIsExceptPages($request->path(), $exceptPages)) {

                // Store visit
                DB::table(config('user-monitoring.visit_monitoring.table'))->insert([
                    'user_id' => UserUtils::getUserId(),
                    'browser_name' => $detector->getBrowser(),
                    'platform' => $detector->getDevice(),
                    'device' => $detector->getDevice(),
                    'ip' => $request->ip(),
                    'user_guard' => UserUtils::getCurrentGuardName(),
                    'page' => $this->mappingPage($request->path()),             // $request->url(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return $next($request);
    }

    /**
     * Check request page are exists in expect pages.
     */
    private function checkIsExceptPages(string $page, array $exceptPages): bool
    {
        // return collect($exceptPages)->contains($page);
        return collect($exceptPages)->contains(function ($validUrl) use ($page) {
            // Convert wildcard pattern to regex
            $pattern = str_replace('*', '.*', $validUrl);

            // Check if the input URL matches the pattern (regular expression)
            return preg_match('#^' . $pattern . '$#', $page);
        });

    }

    /**
     * Mapping page
     */
    private function mappingPage(string $path) {

        if ($path == '/') {
            $path = 'home';
        } else if (preg_match("/\/\d+\//", $path)) {
            // e.g. annual-campaign/12788/summary
            $path = preg_replace("/\/\d+\//", "/", $path);
        } else if (preg_match("/\/\d+$/", $path)) {
            // e.g. volunteering/profile/177
            $path = preg_replace('/\/\d+$/', '', $path);
        }

        return $path; 
    }

}
