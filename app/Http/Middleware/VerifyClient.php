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

        $apiKey = $project->getSetting('api_key');

        if (! ($request->header('X-Lexicon-API-Key') === $apiKey)) {
            throw new AuthenticationException();
        }

        return $next($request);
    }
}
