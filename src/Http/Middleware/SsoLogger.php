<?php

namespace Northwestern\SysDev\SOA\Http\Middleware;

use App\Access;
use Auth;
use Closure;
use http\Exception;
use http\Exception\RuntimeException;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Illuminate\Support\Facades\Log;
use Northwestern\SysDev\SOA\Auth\SSOLoggable;

class SsoLogger extends Middleware
{
    protected $max_logs;

    public function __construct()
    {
        $this->max_logs = config('nusoa.sso.db_log_max_entries');
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $user = Auth::user();
        if ($user == null) {
            $this->log_request($request);
        } else {
            $this->log_request($request, $user);
        }
        if (Access::count() > $this->max_logs) {
            $this->remove_oldest_logs();
        };
        return $next($request);
    }

    private function log_request($request,  $user=NULL)
    {

        if (($user != NULL) && (!in_array('Northwestern\SysDev\SOA\Auth\SSOLoggable',class_uses($user)))) {
            dd('You must implement the Northwestern\SysDev\SOA\Auth\SSOLoggable trait on your user 
            model for sso logging to work. See the documentation for setup instructions.');
        }

        $path = $request->path();

        try {
            $agent = $request->header('User-Agent');
        } catch (Exception $x) {
            $agent = 'null';
        }

        $access = new Access(['path' => $path, 'agent' => $agent]);
        $time = Time();

        if ($user != null) {
            $access->primary_user_id = $user->getAuthIdentifier();
            $identifier = $user->identifiers();
            Log::info("Access $identifier agent=$agent path=$path time=$time");
        }

        if (config('nusoa.sso.db_log_enabled') == 'true') {
            $access->save();
        }
        return;
    }

    private function remove_oldest_logs()
    {
        // Remove between 1000 and 5000 logs at a time. Scaled roughly by max logs set.
        // I don't want to remove more than 5000 and risk slowing down the request significantly.
        $num_to_remove = max(min(5, $this->max_logs / 10), 1);
        Access::orderBy('created_at', 'asc')->take($num_to_remove)->delete();
    }
}