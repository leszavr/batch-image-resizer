<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;

class PlanCapabilitiesResolver
{
    public function forUser(?User $user): array
    {
        $plan = $user?->effectivePlan() ?? $this->getFreePlan();

        return $this->forPlan($plan, $user?->hasUnlimitedAccess() ?? false);
    }

    public function forPlan(Plan $plan, bool $hasUnlimitedAccess = false): array
    {
        $allFormats = array_values((array) config('ipp.output_formats', []));
        $allSteps = array_values((array) config('ipp.pipeline_steps', []));
        $featureFlags = $this->normalizeList($plan->feature_flags);

        $allowedFormats = $this->normalizeList($plan->allowed_formats);
        $allowedSteps = $this->normalizeList($plan->allowed_operations);

        if ($hasUnlimitedAccess) {
            $allowedFormats = $allFormats;
            $allowedSteps = $allSteps;
        } else {
            $allowedFormats = $allowedFormats === [] ? $allFormats : array_values(array_intersect($allFormats, $allowedFormats));
            $allowedSteps = $allowedSteps === [] ? $allSteps : array_values(array_intersect($allSteps, $allowedSteps));
        }

        return [
            'output_formats' => $allowedFormats,
            'pipeline_steps' => $allowedSteps,
            'feature_flags' => $featureFlags,
            'watermark' => $hasUnlimitedAccess ? true : $this->hasFeature($featureFlags, 'watermark', (bool) $plan->watermark),
            'priority_queue' => $hasUnlimitedAccess ? true : $this->hasFeature($featureFlags, 'priority_queue', (bool) $plan->priority_queue),
            'api_access' => $hasUnlimitedAccess ? true : $this->hasFeature($featureFlags, 'api_access', (bool) $plan->api_access),
            'ai_features' => $hasUnlimitedAccess ? true : $this->hasFeature($featureFlags, 'ai_features', false),
            'monthly_credits' => (int) ($plan->monthly_credits ?? 0),
        ];
    }

    private function hasFeature(array $flags, string $feature, bool $fallback): bool
    {
        if ($flags === []) {
            return $fallback;
        }

        return in_array($feature, $flags, true);
    }

    private function normalizeList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $item) => is_string($item) ? strtolower(trim($item)) : '',
            $value
        )));
    }

    private function getFreePlan(): Plan
    {
        return Plan::query()->where('slug', 'free')->first() ?? new Plan([
                'allowed_formats' => (array) config('ipp.output_formats', ['jpg', 'png']),
                'allowed_operations' => (array) config('ipp.pipeline_steps', ['resize', 'rotate', 'flip', 'crop', 'filter']),
            'watermark' => false,
            'priority_queue' => false,
            'api_access' => false,
            'monthly_credits' => 0,
        ]);
    }
}
