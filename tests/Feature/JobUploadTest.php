<?php

namespace Tests\Feature;

use App\Jobs\ProcessImageJob;
use App\Models\ImageJob;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('local');
    }

    public function test_guest_can_create_job_from_web_form(): void
    {
        Queue::fake();

        $response = $this->post(route('jobs.store'), [
            'files' => [
                UploadedFile::fake()->image('first.jpg', 1200, 800)->size(400),
                UploadedFile::fake()->image('second.png', 800, 800)->size(300),
            ],
            'output_format' => 'jpg',
            'output_quality' => 82,
            'resize_mode' => 'width',
            'resize_width' => 1200,
            'rename_mode' => 'sequence',
            'rename_prefix' => 'new_',
            'rename_suffix' => '_done',
            'rename_start_number' => 7,
        ]);

        $job = ImageJob::query()->first();

        $response
            ->assertRedirect(route('jobs.show', $job->uuid));

        $this->assertNotNull($job);
        $this->assertSame(ImageJob::STATUS_PENDING, $job->status);
        $this->assertSame(2, $job->total_files);
        $this->assertSame('jpg', $job->output_format);
        $this->assertSame(82, $job->output_quality);
        $this->assertSame('sequence', $job->rename_mode);
        $this->assertSame('new_', $job->rename_prefix);
        $this->assertSame('_done', $job->rename_suffix);
        $this->assertSame(7, $job->rename_start_number);
        $this->assertCount(2, $job->files);
        $this->assertSame('new_007_done.jpg', $job->buildResultFilename('IMG_20260120_122251.jpg', 0));
        $this->assertSame('new_008_done.jpg', $job->buildResultFilename('IMG_20260120_122300.jpg', 1));

        Queue::assertPushed(ProcessImageJob::class, function (ProcessImageJob $queuedJob) use ($job) {
            return $queuedJob->imageJobId === $job->id;
        });
    }

    public function test_web_form_returns_json_payload_for_ajax_submit(): void
    {
        Queue::fake();

        $response = $this->postJson(route('jobs.store'), [
            'files' => [
                UploadedFile::fake()->image('ajax.jpg', 640, 480)->size(250),
            ],
            'output_format' => 'webp',
        ]);

        $job = ImageJob::query()->first();

        $response
            ->assertCreated()
            ->assertJson([
                'message' => dbt('jobs.messages.created'),
                'uuid' => $job->uuid,
                'redirect_url' => route('jobs.show', $job->uuid),
            ]);
    }

    public function test_job_status_endpoint_returns_progress_payload_for_job_owner(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('jobs.store'), [
            'files' => [UploadedFile::fake()->image('status.jpg', 100, 100)],
            'output_format' => 'jpg',
        ]);

        $job = ImageJob::query()->firstOrFail();

        $this->assertSame($user->id, $job->user_id);

        $statusResponse = $this->getJson(route('jobs.status', $job->uuid));

        $statusResponse
            ->assertOk()
            ->assertJson([
                'status' => ImageJob::STATUS_PENDING,
                'processed_files' => 0,
                'failed_files' => 0,
                'total_files' => 1,
                'is_finished' => false,
            ]);
    }

    public function test_superadmin_can_bypass_plan_limits_without_changing_plan(): void
    {
        Queue::fake();

        $freePlan = Plan::query()->where('slug', 'free')->firstOrFail();

        $user = User::factory()->create([
            'plan_id' => $freePlan->id,
        ]);
        $user->assignRole(User::SUPERADMIN_ROLE);

        ImageJob::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'status' => ImageJob::STATUS_DONE,
            'pipeline' => [],
            'total_files' => 0,
            'processed_files' => 0,
            'failed_files' => 0,
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ImageJob::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'status' => ImageJob::STATUS_DONE,
            'pipeline' => [],
            'total_files' => 0,
            'processed_files' => 0,
            'failed_files' => 0,
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ImageJob::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'status' => ImageJob::STATUS_DONE,
            'pipeline' => [],
            'total_files' => 0,
            'processed_files' => 0,
            'failed_files' => 0,
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('jobs.store'), [
            'files' => [
                UploadedFile::fake()->image('one.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('two.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('three.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('four.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('five.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('six.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('seven.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('eight.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('nine.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('ten.jpg', 100, 100)->size(100),
                UploadedFile::fake()->image('eleven.jpg', 100, 100)->size(100),
            ],
            'output_format' => 'jpg',
        ]);

        $response->assertRedirect();
        $this->assertTrue($user->fresh()->hasUnlimitedAccess());
        $this->assertSame($freePlan->id, $user->fresh()->plan_id);

        $job = ImageJob::query()->latest('id')->firstOrFail();
        $this->assertSame(11, $job->total_files);

        Queue::assertPushed(ProcessImageJob::class, function (ProcessImageJob $queuedJob) use ($job) {
            return $queuedJob->imageJobId === $job->id;
        });
    }
}
