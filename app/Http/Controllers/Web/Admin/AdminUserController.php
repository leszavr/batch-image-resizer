<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageJob;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::query()->orderBy('sort_order')->orderBy('id')->get();
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        $users = User::query()
            ->with(['plan', 'roles'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->string('q')->toString());

                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'plans', 'roles'));
    }

    public function create()
    {
        $plans = Plan::query()->orderBy('sort_order')->orderBy('id')->get();
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.users.create', compact('plans', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'plan_id' => 'nullable|exists:plans,id',
            'credits_balance' => 'required|integer|min:0|max:1000000000',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'unlimited_access' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'plan_id' => $data['plan_id'] ?? null,
            'credits_balance' => $data['credits_balance'],
            'unlimited_access' => $data['unlimited_access'] ?? false,
        ]);

        $roles = collect($data['roles'] ?? [])->filter()->values()->all();
        if ($roles === []) {
            $roles = ['user'];
        }
        $user->syncRoles($roles);

        return redirect()
            ->route('admin.users.index')
            ->with('success', dbt('admin.users.messages.created', ['email' => $user->email]));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'plan_id' => 'nullable|exists:plans,id',
            'credits_balance' => 'required|integer|min:0|max:1000000000',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'unlimited_access' => 'nullable|boolean',
            'is_blocked' => 'nullable|boolean',
            'blocked_until' => 'nullable|date',
            'block_reason' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'plan_id' => $data['plan_id'] ?? null,
            'credits_balance' => $data['credits_balance'],
            'unlimited_access' => $data['unlimited_access'] ?? false,
            'is_blocked' => $data['is_blocked'] ?? false,
            'blocked_until' => $data['blocked_until'] ?? null,
            'block_reason' => $data['block_reason'] ?? null,
        ]);

        $roles = collect($data['roles'] ?? [])->filter()->values()->all();
        if ($roles === []) {
            $roles = ['user'];
        }

        if ($user->id === auth()->id() && ! in_array(User::SUPERADMIN_ROLE, $roles, true)) {
            $roles[] = User::SUPERADMIN_ROLE;
        }

        $user->syncRoles($roles);

        return redirect()
            ->route('admin.users.index')
            ->with('success', dbt('admin.users.messages.updated', ['email' => $user->email]));
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => 'required|string|min:8|max:255',
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', dbt('admin.users.messages.password_reset', ['email' => $user->email]));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', dbt('admin.users.messages.cannot_delete_self'));
        }

        $email = $user->email;

        // Delete related data
        $user->imageJobs()->delete();
        $user->subscriptions()->delete();
        $user->presets()->delete();

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', dbt('admin.users.messages.deleted', ['email' => $email]));
    }

    public function stats(Request $request, User $user)
    {
        $period = $request->input('period', '30');

        $stats = [
            'user' => $user,
            'total_jobs' => $user->imageJobs()->count(),
            'jobs_today' => $user->todayJobsCount(),
            'jobs_this_week' => $user->imageJobs()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'jobs_this_month' => $user->imageJobs()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'total_storage_used' => $this->calculateStorageUsed($user),
            'recent_jobs' => $user->imageJobs()->withCount('files')->latest()->limit(10)->get(),
            'jobs_chart' => $this->getJobsChartData($user, (int) $period),
        ];

        return view('admin.users.stats', compact('stats', 'period'));
    }

    private function calculateStorageUsed(User $user): int
    {
        return (int) $user->imageJobs()
            ->whereNotNull('result_archive_path')
            ->sum('result_archive_size');
    }

    private function getJobsChartData(User $user, int $days): array
    {
        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d.m');

            $count = $user->imageJobs()
                ->whereDate('created_at', $date)
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}