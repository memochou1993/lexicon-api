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

        if (! ($request->header('X-Localize-Secret-Key') === $secret_key)) {
            abort(401, __('error.secret_key'));
        }

        return $next($request);
    }
}
