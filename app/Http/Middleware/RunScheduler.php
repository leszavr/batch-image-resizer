<?php

namespace App\Http\Middleware;

use App\Services\SchedulerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RunScheduler
{
    public function __construct(private readonly SchedulerService $schedulerService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Run the scheduler on a small percentage of requests
        // This ensures it runs periodically without system cron
        if (rand(1, 100) <= 5) { // 5% chance
            $this->schedulerService->runScheduler();
        }

        return $next($request);
    }
}