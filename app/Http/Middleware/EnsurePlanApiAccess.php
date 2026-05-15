<?php

namespace App\Http\Middleware;

use App\Services\PlanCapabilitiesResolver;
use Closure;
use Illuminate\Http\Request;

class EnsurePlanApiAccess
{
    public function __construct(private readonly PlanCapabilitiesResolver $capabilitiesResolver) {}

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $capabilities = $this->capabilitiesResolver->forUser($user);

        if (! ($capabilities['api_access'] ?? false)) {
            return response()->json([
                'message' => dbt('api.access_denied'),
            ], 403);
        }

        return $next($request);
    }
}
