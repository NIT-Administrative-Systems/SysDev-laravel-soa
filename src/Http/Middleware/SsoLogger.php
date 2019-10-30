<?php

namespace Northwestern\SysDev\SOA\Http\Middleware;

use http\Env;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Auth;
use Closure;

class SsoLogger extends Middleware
{

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
        return $next($request);
    }

    private function log_request($anonymous, $request) {
        $path = $request->path();

        try {
            $agent = $request->header('User-Agent');
        }  catch (Exception $x) {
            $agent = null;
        }

        $access = new \App\Access(['path'=>$path,'agent'=>$agent]);
        if (!$anonymous) {
            $access->netid = Auth::user()['netid'];
        }
        $access->save();
        return;
    }
}