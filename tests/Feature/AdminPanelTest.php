<?php

namespace Tests\Feature;

use App\Models\ImageJob;
use App\Models\ImageJobFile;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('local');
    }

    public function test_regular_user_cannot_open_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_superadmin_can_open_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(User::SUPERADMIN_ROLE);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(dbt('admin.common.title'));
    }

    public function test_superadmin_can_cleanup_expired_archives_and_result_files(): void
    {
        $user = User::factory()->create();
        $user->assignRole(User::SUPERADMIN_ROLE);

        Storage::disk('local')->put('archives/expired-job.zip', 'zip');
        Storage::disk('local')->put('results/expired-job/result.jpg', 'image');

        $job = ImageJob::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'status' => ImageJob::STATUS_DONE,
            'pipeline' => [],
            'total_files' => 1,
            'processed_files' => 1,
            'failed_files' => 0,
            'result_archive_path' => 'archives/expired-job.zip',
            'result_size_bytes' => 123,
            'expires_at' => now()->subHour(),
        ]);

        ImageJobFile::query()->create([
            'image_job_id' => $job->id,
            'original_name' => 'source.jpg',
            'original_path' => 'uploads/source.jpg',
            'result_path' => 'results/expired-job/result.jpg',
            'original_size' => 100,
            'result_size' => 90,
            'status' => ImageJobFile::STATUS_DONE,
            'sort_order' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('admin.jobs.cleanup-expired'))
            ->assertRedirect(route('admin.jobs.index'));

        $job->refresh();

        $this->assertSame(ImageJob::STATUS_EXPIRED, $job->status);
        $this->assertNull($job->result_archive_path);
        $this->assertSame(0, $job->result_size_bytes);
        Storage::disk('local')->assertMissing('archives/expired-job.zip');
        Storage::disk('local')->assertMissing('results/expired-job/result.jpg');
    }

    public function test_superadmin_can_update_plan_in_admin_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole(User::SUPERADMIN_ROLE);

        $plan = Plan::query()->where('slug', 'free')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.plans.update', $plan), [
                'name' => 'Free+',
                'slug' => 'free',
                'description' => 'Updated free plan',
                'price_month' => 0,
                'price_year' => 0,
                'currency' => 'rub',
                'max_files_per_job' => 15,
                'max_file_size_mb' => 20,
                'daily_jobs_limit' => 5,
                'monthly_credits' => 10,
                'sort_order' => 0,
                'allowed_formats' => 'jpg, png, webp',
                'allowed_operations' => 'resize, rotate',
                'watermark' => '1',
                'api_access' => '0',
                'priority_queue' => '0',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.plans.index'));

        $plan->refresh();

        $this->assertSame('Free+', $plan->name);
        $this->assertSame(15, $plan->max_files_per_job);
        $this->assertSame(20, $plan->max_file_size_mb);
        $this->assertSame(5, $plan->daily_jobs_limit);
        $this->assertSame(['jpg', 'png', 'webp'], $plan->allowed_formats);
        $this->assertSame(['resize', 'rotate'], $plan->allowed_operations);
        $this->assertSame('RUB', $plan->currency);
    }

    public function test_superadmin_can_update_user_roles_plan_and_credits(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(User::SUPERADMIN_ROLE);

        $managedUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'credits_balance' => 5,
        ]);
        $managedUser->assignRole('user');

        $proPlan = Plan::query()->where('slug', 'pro')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $managedUser), [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
                'plan_id' => $proPlan->id,
                'credits_balance' => 250,
                'roles' => ['user', 'admin'],
            ])
            ->assertRedirect(route('admin.users.index'));

        $managedUser->refresh();

        $this->assertSame('Updated User', $managedUser->name);
        $this->assertSame('updated@example.com', $managedUser->email);
        $this->assertSame($proPlan->id, $managedUser->plan_id);
        $this->assertSame(250, $managedUser->credits_balance);
        $this->assertTrue($managedUser->hasRole('admin'));
        $this->assertFalse($managedUser->hasRole(User::SUPERADMIN_ROLE));
    }

    public function test_superadmin_can_bulk_delete_jobs_with_related_files(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(User::SUPERADMIN_ROLE);

        Storage::disk('local')->put('archives/job-a.zip', 'zip-a');
        Storage::disk('local')->put('results/job-a/result.jpg', 'result-a');
        Storage::disk('local')->put('uploads/job-a/source.jpg', 'source-a');

        Storage::disk('local')->put('archives/job-b.zip', 'zip-b');
        Storage::disk('local')->put('results/job-b/result.jpg', 'result-b');
        Storage::disk('local')->put('uploads/job-b/source.jpg', 'source-b');

        $jobA = ImageJob::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $admin->id,
            'status' => ImageJob::STATUS_DONE,
            'pipeline' => [],
            'total_files' => 1,
            'processed_files' => 1,
            'failed_files' => 0,
            'result_archive_path' => 'archives/job-a.zip',
            'result_size_bytes' => 100,
            'expires_at' => now()->addHour(),
        ]);

        $jobB = ImageJob::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $admin->id,
            'status' => ImageJob::STATUS_DONE,
            'pipeline' => [],
            'total_files' => 1,
            'processed_files' => 1,
            'failed_files' => 0,
            'result_archive_path' => 'archives/job-b.zip',
            'result_size_bytes' => 100,
            'expires_at' => now()->addHour(),
        ]);

        foreach ([[$jobA, 'job-a'], [$jobB, 'job-b']] as [$job, $slug]) {
            ImageJobFile::query()->create([
                'image_job_id' => $job->id,
                'original_name' => 'source.jpg',
                'original_path' => "uploads/{$slug}/source.jpg",
                'result_path' => "results/{$slug}/result.jpg",
                'original_size' => 100,
                'result_size' => 90,
                'status' => ImageJobFile::STATUS_DONE,
                'sort_order' => 0,
            ]);
        }

        $this->actingAs($admin)
            ->post(route('admin.jobs.bulk-destroy'), [
                'job_ids' => [$jobA->id, $jobB->id],
            ])
            ->assertRedirect(route('admin.jobs.index'));

        $this->assertDatabaseMissing('image_jobs', ['id' => $jobA->id]);
        $this->assertDatabaseMissing('image_jobs', ['id' => $jobB->id]);
        $this->assertDatabaseCount('image_job_files', 0);

        Storage::disk('local')->assertMissing('archives/job-a.zip');
        Storage::disk('local')->assertMissing('results/job-a/result.jpg');
        Storage::disk('local')->assertMissing('uploads/job-a/source.jpg');
        Storage::disk('local')->assertMissing('archives/job-b.zip');
        Storage::disk('local')->assertMissing('results/job-b/result.jpg');
        Storage::disk('local')->assertMissing('uploads/job-b/source.jpg');
    }
}
