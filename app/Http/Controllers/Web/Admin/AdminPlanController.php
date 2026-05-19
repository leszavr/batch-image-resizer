<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageJob;
use App\Models\Locale;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\LocaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminPlanController extends Controller
{
    public function __construct(private readonly LocaleService $localeService) {}

    public function index()
    {
        $plans = Plan::query()->orderBy('sort_order')->orderBy('id')->get();
        $locales = Locale::query()->orderByDesc('is_default')->orderBy('name')->get();
        $defaultLocale = $this->localeService->defaultLocale();
        $formatOptions = array_values((array) config('ipp.output_formats', []));
        $operationOptions = array_values((array) config('ipp.pipeline_steps', []));
        $featureOptions = [
            'watermark' => dbt('admin.plans.features.watermark'),
            'api_access' => dbt('admin.plans.features.api_access'),
            'priority_queue' => dbt('admin.plans.features.priority_queue'),
            'ai_features' => dbt('admin.plans.features.ai_features'),
            'is_active' => dbt('admin.plans.features.is_active'),
            'is_popular' => dbt('admin.plans.features.is_popular'),
        ];

        $jobsByPlan = ImageJob::query()
            ->join('users', 'users.id', '=', 'image_jobs.user_id')
            ->select('users.plan_id', DB::raw('COUNT(*) as jobs_count'))
            ->whereNotNull('users.plan_id')
            ->groupBy('users.plan_id')
            ->pluck('jobs_count', 'users.plan_id');

        $subscriptionsByPlan = Subscription::query()
            ->select('plan_id', DB::raw('COUNT(*) as subscriptions_count'))
            ->whereIn('status', ['active', 'trial'])
            ->groupBy('plan_id')
            ->pluck('subscriptions_count', 'plan_id');

        $usersByPlan = DB::table('users')
            ->select('plan_id', DB::raw('COUNT(*) as users_count'))
            ->whereNotNull('plan_id')
            ->groupBy('plan_id')
            ->pluck('users_count', 'plan_id');

        $analytics = [];

        foreach ($plans as $plan) {
            $monthlyRevenue = (int) ($subscriptionsByPlan[$plan->id] ?? 0) * (int) $plan->price_month;

            $analytics[$plan->id] = [
                'users_count' => (int) ($usersByPlan[$plan->id] ?? 0),
                'jobs_count' => (int) ($jobsByPlan[$plan->id] ?? 0),
                'subscriptions_count' => (int) ($subscriptionsByPlan[$plan->id] ?? 0),
                'monthly_revenue' => $monthlyRevenue,
            ];
        }

        return view('admin.plans.index', compact(
            'plans',
            'locales',
            'defaultLocale',
            'analytics',
            'formatOptions',
            'operationOptions',
            'featureOptions'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validatePlan($request);

        Plan::query()->create($this->buildPayload($request, $data));

        return redirect()
            ->route('admin.plans.index')
            ->with('success', dbt('admin.plans.messages.created'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $this->validatePlan($request, $plan);

        $plan->update($this->buildPayload($request, $data));

        return redirect()
            ->route('admin.plans.index')
            ->with('success', dbt('admin.plans.messages.updated', ['name' => $plan->localizedName()]));
    }

    public function destroy(Plan $plan)
    {
        $usersCount = $plan->users()->count();
        $subscriptionsCount = $plan->subscriptions()->count();

        if ($usersCount > 0 || $subscriptionsCount > 0) {
            return redirect()
                ->route('admin.plans.index')
                ->with('error', dbt('admin.plans.messages.delete_blocked'));
        }

        $name = $plan->name;
        $plan->delete();

        return redirect()
            ->route('admin.plans.index')
            ->with('success', dbt('admin.plans.messages.deleted', ['name' => $name]));
    }

    private function validatePlan(Request $request, ?Plan $plan = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'slug')->ignore($plan?->id),
            ],
            'description' => 'nullable|string',
            'price_month' => 'required|integer|min:0',
            'price_year' => 'required|integer|min:0',
            'currency' => 'required|string|size:3',
            'max_files_per_job' => 'required|integer|min:1|max:10000',
            'max_file_size_mb' => 'required|integer|min:1|max:100000',
            'daily_jobs_limit' => 'required|integer|min:1|max:1000000',
            'monthly_credits' => 'required|integer|min:0|max:1000000000',
            'storage_ttl_hours' => 'required|integer|min:1|max:8760', // Max 1 year
            'sort_order' => 'required|integer|min:0|max:100000',
            'allowed_formats' => 'nullable',
            'allowed_operations' => 'nullable',
            'feature_flags' => 'nullable',
            'name_translations' => 'nullable|array',
            'name_translations.*' => 'nullable|string|max:255',
            'description_translations' => 'nullable|array',
            'description_translations.*' => 'nullable|string',
            'watermark' => 'nullable|boolean',
            'api_access' => 'nullable|boolean',
            'priority_queue' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
        ]);
    }

    private function buildPayload(Request $request, array $data): array
    {
        $featureFlags = $this->normalizeListInput($data['feature_flags'] ?? null);
        $isPopular = in_array('is_popular', $featureFlags, true) || $request->boolean('is_popular');

        if ($isPopular) {
            Plan::query()->where('is_popular', true)->update(['is_popular' => false]);
        }

        return [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'name_translations' => $this->normalizeTranslations($data['name_translations'] ?? []),
            'description_translations' => $this->normalizeTranslations($data['description_translations'] ?? []),
            'price_month' => $data['price_month'],
            'price_year' => $data['price_year'],
            'currency' => strtoupper($data['currency']),
            'max_files_per_job' => $data['max_files_per_job'],
            'max_file_size_mb' => $data['max_file_size_mb'],
            'daily_jobs_limit' => $data['daily_jobs_limit'],
            'monthly_credits' => $data['monthly_credits'],
            'storage_ttl_hours' => $data['storage_ttl_hours'],
            'sort_order' => $data['sort_order'],
            'allowed_formats' => $this->normalizeListInput($data['allowed_formats'] ?? null),
            'allowed_operations' => $this->normalizeListInput($data['allowed_operations'] ?? null),
            'feature_flags' => $featureFlags,
            'watermark' => in_array('watermark', $featureFlags, true) || $request->boolean('watermark'),
            'api_access' => in_array('api_access', $featureFlags, true) || $request->boolean('api_access'),
            'priority_queue' => in_array('priority_queue', $featureFlags, true) || $request->boolean('priority_queue'),
            'is_active' => in_array('is_active', $featureFlags, true) || $request->boolean('is_active'),
            'is_popular' => $isPopular,
        ];
    }

    private function normalizeListInput(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(
                static fn (mixed $item) => is_string($item) ? trim($item) : '',
                $value
            )));
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $item) => trim($item),
            explode(',', $value)
        )));
    }

    private function normalizeTranslations(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $translations = [];

        foreach ($value as $locale => $translation) {
            $localeCode = trim((string) $locale);
            $text = trim((string) $translation);

            if ($localeCode === '' || $text === '') {
                continue;
            }

            $translations[$localeCode] = $text;
        }

        return $translations;
    }
}