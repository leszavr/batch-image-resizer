<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageJob;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', '30');
        $days = (int) $period;

        $stats = [
            // Общая статистика
            'total_users' => User::count(),
            'total_jobs' => ImageJob::count(),
            'active_subscriptions' => Subscription::whereIn('status', ['active', 'trial'])->count(),
            'total_revenue' => $this->calculateTotalRevenue(),

            // Статистика за сегодня
            'users_today' => User::whereDate('created_at', today())->count(),
            'jobs_today' => ImageJob::whereDate('created_at', today())->count(),

            // Статистика по статусам
            'jobs_by_status' => [
                'pending' => ImageJob::where('status', 'pending')->count(),
                'processing' => ImageJob::where('status', 'processing')->count(),
                'done' => ImageJob::where('status', 'done')->count(),
                'failed' => ImageJob::where('status', 'failed')->count(),
            ],

            // Топ пользователи
            'top_users' => User::query()
                ->withCount('imageJobs')
                ->orderByDesc('image_jobs_count')
                ->limit(10)
                ->get(),

            // Графики
            'users_chart' => $this->getUsersChartData($days),
            'jobs_chart' => $this->getJobsChartData($days),
            'revenue_chart' => $this->getRevenueChartData($days),
        ];

        return view('admin.statistics.index', compact('stats', 'period'));
    }

    private function getUsersChartData(int $days): array
    {
        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d.m');

            $count = User::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getJobsChartData(int $days): array
    {
        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d.m');

            $count = ImageJob::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getRevenueChartData(int $days): array
    {
        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d.m');

            // Calculate revenue based on plan prices
            $amount = Subscription::whereDate('subscriptions.created_at', $date)
                ->where('subscriptions.status', 'active')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->sum(DB::raw('CASE 
                    WHEN subscriptions.billing_period = "year" THEN plans.price_year 
                    ELSE plans.price_month 
                END'));

            $data[] = $amount;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function calculateTotalRevenue(): float
    {
        // Calculate total revenue based on plan prices
        $monthlyRevenue = Subscription::where('subscriptions.status', 'active')
            ->where('subscriptions.billing_period', 'month')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price_month');

        $yearlyRevenue = Subscription::where('subscriptions.status', 'active')
            ->where('subscriptions.billing_period', 'year')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price_year');

        // Convert yearly to monthly equivalent for display
        return $monthlyRevenue + ($yearlyRevenue / 12);
    }
}
