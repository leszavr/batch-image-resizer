<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'name_translations', 'description_translations',
        'price_month', 'price_year', 'currency',
        'max_files_per_job', 'max_file_size_mb', 'daily_jobs_limit',
        'monthly_credits', 'storage_ttl_hours', 'watermark', 'api_access', 'priority_queue',
        'allowed_formats', 'allowed_operations', 'feature_flags', 'is_active', 'is_popular', 'sort_order',
    ];

    protected $casts = [
        'name_translations'   => 'array',
        'description_translations' => 'array',
        'allowed_formats'    => 'array',
        'allowed_operations' => 'array',
        'feature_flags'      => 'array',
        'watermark'          => 'boolean',
        'api_access'         => 'boolean',
        'priority_queue'     => 'boolean',
        'is_active'          => 'boolean',
        'is_popular'         => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isFree(): bool
    {
        return $this->slug === 'free';
    }

    public function priceMonthFormatted(): string
    {
        if ($this->price_month === 0) {
            return dbt('plans.price.free');
        }

        return number_format($this->price_month / 100, 0, '.', ' ')
            . ' '
            . $this->currency
            . '/'
            . dbt('plans.price.month_short');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        return $this->name_translations[$locale]
            ?? $this->name_translations[$fallback]
            ?? $this->name;
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        return $this->description_translations[$locale]
            ?? $this->description_translations[$fallback]
            ?? $this->description;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getStorageTtlFormattedAttribute(): string
    {
        $hours = $this->storage_ttl_hours;
        
        if ($hours < 24) {
            return $hours . ' ' . dbt('plans.storage.hours');
        }
        
        $days = floor($hours / 24);
        $remainingHours = $hours % 24;
        
        if ($remainingHours === 0) {
            if ($days < 30) {
                return $days . ' ' . dbt('plans.storage.days');
            }
            
            $months = floor($days / 30);
            $remainingDays = $days % 30;
            
            if ($remainingDays === 0) {
                return $months . ' ' . dbt('plans.storage.months');
            }
            
            return $months . ' ' . dbt('plans.storage.months') . ' ' . $remainingDays . ' ' . dbt('plans.storage.days');
        }
        
        return $days . ' ' . dbt('plans.storage.days') . ' ' . $remainingHours . ' ' . dbt('plans.storage.hours');
    }
}