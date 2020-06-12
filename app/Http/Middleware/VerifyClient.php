<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Auth\AuthenticationException;

class VerifyClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        /** @var Project $project */
        $project = $request->route('project');

        $secretKey = $project->getSetting('secret_key');

        if (! ($request->header('X-Localize-Secret-Key') === $secretKey)) {
            throw new AuthenticationException();
        }

        return $next($request);
    }
}
