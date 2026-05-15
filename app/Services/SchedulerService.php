<?php

namespace App\Services;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SchedulerService
{
    private Schedule $schedule;
    private const CACHE_KEY = 'scheduler_last_run';
    private const RUN_INTERVAL = 300; // 5 minutes

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Run the scheduler without requiring system cron
     */
    public function runScheduler(): void
    {
        $lastRun = Cache::get(self::CACHE_KEY, 0);
        $now = time();

        if ($now - $lastRun < self::RUN_INTERVAL) {
            return; // Don't run too frequently
        }

        try {
            $events = $this->schedule->dueEvents(app());

            foreach ($events as $event) {
                if ($event->isDue(app())) {
                    $event->run(app());
                    
                    Log::info('Scheduler: Run scheduled command', [
                        'command' => $event->command,
                        'expression' => $event->expression,
                    ]);
                }
            }

            Cache::put(self::CACHE_KEY, $now, self::RUN_INTERVAL * 2);
        } catch (\Exception $e) {
            Log::error('Scheduler: Error running scheduled commands', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get scheduler status information
     */
    public function getSchedulerStatus(): array
    {
        $lastRun = Cache::get(self::CACHE_KEY, 0);
        $nextRun = $lastRun + self::RUN_INTERVAL;
        
        $events = $this->schedule->events();
        $dueEvents = [];

        foreach ($events as $event) {
            if ($event->isDue(app())) {
                $dueEvents[] = [
                    'command' => $event->command,
                    'expression' => $event->expression,
                    'next_run' => $event->nextRunDate()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return [
            'last_run' => $lastRun ? date('Y-m-d H:i:s', $lastRun) : 'Never',
            'next_run' => date('Y-m-d H:i:s', $nextRun),
            'due_events_count' => count($dueEvents),
            'due_events' => $dueEvents,
            'total_events' => count($events),
            'is_due' => count($dueEvents) > 0,
        ];
    }

    /**
     * Force run the scheduler immediately
     */
    public function runNow(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->runScheduler();
    }
}