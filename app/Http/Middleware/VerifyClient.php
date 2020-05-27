<?php

namespace App\Http\Middleware;

use Closure;

class VerifyClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $project = $request->route('project');

        $secret_key = json_decode($project->api_keys)->secret_key;

        if ($secret_key !== $request->header('X-Localize-Secret-Key')) {
            abort(401, __('error.secret_key'));
        }

        return $next($request);
    }
}
