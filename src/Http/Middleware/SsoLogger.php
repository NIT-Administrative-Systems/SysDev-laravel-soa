<?php

namespace Northwestern\SysDev\SOA\Http\Middleware;

use http\ENV;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Auth;
use App\Access;
use Closure;

class SsoLogger extends Middleware
{
    protected $max_logs;

    public function __construct() { $this->max_logs = ENV('SSO_LOG_MAX_ENTRIES',1000000); }

    public function handle($request, Closure $next, ...$guards)
    {
        if (env('SSO_LOG_ENABLED') != 'true') { return $next($request); }

        $controller = $request->route()->controller;

        if (class_basename($controller) == "WebSSOController") {
            // They're doing auth right now so minimal log
            $this->log_request(true, $request);
            return $next($request);
        } else {
            if (Auth()->user() == null) {
                $this->log_request(true, $request);
            } else {
                $this->log_request(false, $request);
            }
        }


        if (sizeof(Access::all()) > $this->max_logs) {
            $this->remove_oldest_logs();
        };

        return $next($request);
    }

    private function log_request($anonymous, $request) {
        $path = $request->path();
        try {
            $agent = $request->header('User-Agent');
        }  catch (Exception $x) {
            $agent = null;
        }

        $access = new Access(['path'=>$path,'agent'=>$agent]);
        if (!$anonymous) {
            $access->netid = Auth::user()['netid'];
        }
        $access->save();
        return;
    }

    private function remove_oldest_logs() {
        // Remove between 1000 and 5000 logs at a time. Scaled roughly by max logs set.
        // I don't want to remove more than 5000 and risk slowing down the request significantly.
        $num_to_remove = max(min(5000,$this->max_logs/10),1000);
        Access::orderBy('created_at','asc')->take($num_to_remove)->get()->each->delete();
        # ^ Probably a better way to do this than individual deletes
    }
}