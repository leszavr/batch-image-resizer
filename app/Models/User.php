<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public const SUPERADMIN_ROLE = 'superadmin';

    protected $fillable = [
        'name', 'email', 'password',
        'plan_id', 'avatar', 'locale', 'credits_balance',
        'is_blocked', 'blocked_until', 'block_reason', 'unlimited_access',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'credits_balance'   => 'integer',
            'is_blocked'        => 'boolean',
            'blocked_until'     => 'datetime',
            'unlimited_access'  => 'boolean',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->whereIn('status', ['active', 'trial'])->latestOfMany();
    }

    public function imageJobs()
    {
        return $this->hasMany(ImageJob::class);
    }

    public function presets()
    {
        return $this->hasMany(Preset::class);
    }

    public function effectivePlan(): Plan
    {
        return $this->plan ?? Plan::where('slug', 'free')->first() ?? new Plan([
            'slug'              => 'free',
            'max_files_per_job' => 10,
            'max_file_size_mb'  => 10,
            'daily_jobs_limit'  => 3,
            'watermark'         => true,
            'api_access'        => false,
            'priority_queue'    => false,
            'storage_ttl_hours' => 24,
        ]);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::SUPERADMIN_ROLE);
    }

    public function hasUnlimitedAccess(): bool
    {
        return $this->unlimited_access || $this->isSuperAdmin();
    }

    public function isBlocked(): bool
    {
        if ($this->is_blocked) {
            return true;
        }

        if ($this->blocked_until && $this->blocked_until->isFuture()) {
            return true;
        }

        return false;
    }

    public function blockStatus(): array
    {
        if ($this->is_blocked) {
            return [
                'blocked' => true,
                'type'    => 'permanent',
                'reason'  => $this->block_reason,
                'until'   => null,
            ];
        }

        if ($this->blocked_until && $this->blocked_until->isFuture()) {
            return [
                'blocked' => true,
                'type'    => 'temporary',
                'reason'  => $this->block_reason,
                'until'   => $this->blocked_until,
            ];
        }

        return [
            'blocked' => false,
            'type'    => null,
            'reason'  => null,
            'until'   => null,
        ];
    }

    public function todayJobsCount(): int
    {
        return $this->imageJobs()->whereDate('created_at', today())->count();
    }

    public function canCreateJob(): bool
    {
        if ($this->isBlocked()) {
            return false;
        }

        if ($this->hasUnlimitedAccess()) {
            return true;
        }

        $plan = $this->effectivePlan();

        return $this->todayJobsCount() < $plan->daily_jobs_limit;
    }

    public static function localizedRoleName(string $role): string
    {
        $key = 'roles.' . $role;
        $translated = dbt($key);

        return $translated === 'ui.' . $key
            ? ucfirst(str_replace(['-', '_'], ' ', $role))
            : $translated;
    }
}