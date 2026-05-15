<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ImageJob;
use App\Models\Preset;
use App\Models\Plan;
use App\Services\ImageJobService;
use App\Services\PlanCapabilitiesResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageJobController extends Controller
{
    public function __construct(
        private readonly ImageJobService $service,
        private readonly PlanCapabilitiesResolver $capabilitiesResolver,
    ) {}

    /** Main page — upload form */
    public function index()
    {
        $plans   = Plan::active()->get();
        $presets = Preset::forUser(auth()->id())->orderBy('is_global', 'desc')->get();
        $capabilities = $this->capabilitiesResolver->forUser(auth()->user());

        return view('app', compact('plans', 'presets', 'capabilities'));
    }

    /** Create job from form upload */
    public function store(Request $request)
    {
        $request->validate([
            'files'              => 'required|array|min:1|max:100',
            'files.*'            => 'required|file|mimes:jpg,jpeg,png,gif,bmp,tiff,tif,webp|max:51200',
            'output_format'      => 'nullable|in:jpg,jpeg,png,webp,avif,gif,tiff',
            'output_quality'     => 'nullable|integer|min:1|max:100',
            'resize_mode'        => 'nullable|in:none,fixed,width,height,fit,cover',
            'resize_width'       => 'nullable|integer|min:1|max:10000',
            'resize_height'      => 'nullable|integer|min:1|max:10000',
            'rotate_direction'   => 'nullable|in:none,left,right,180',
            'flip_axis'          => 'nullable|in:none,horizontal,vertical,both',
            'crop_width'         => 'nullable|integer|min:1',
            'crop_height'        => 'nullable|integer|min:1',
            'crop_position'      => 'nullable|in:center,top,bottom,left,right,top-left,top-right,bottom-left,bottom-right',
            'rename_mode'        => 'nullable|in:original,sequence',
            'rename_prefix'      => 'nullable|string|max:50',
            'rename_suffix'      => 'nullable|string|max:50',
            'rename_start_number'=> 'nullable|integer|min:1|max:999999',
        ]);

        $job = $this->service->create(
            options:   $request->except('files'),
            files:     $request->file('files'),
            user:      auth()->user(),
            sessionId: $request->session()->getId(),
        );

        $redirectUrl = route('jobs.show', $job->uuid);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => dbt('jobs.messages.created'),
                'uuid' => $job->uuid,
                'redirect_url' => $redirectUrl,
            ], 201);
        }

        return redirect()->to($redirectUrl)
            ->with('success', dbt('jobs.messages.created'));
    }

    /** Job status/result page */
    public function show(ImageJob $imageJob)
    {
        // Allow access to owner or same session
        $this->authorizeJobAccess($imageJob);

        $imageJob->load('files');
        return view('job', compact('imageJob'));
    }

    /** Poll status (AJAX) */
    public function status(ImageJob $imageJob)
    {
        $this->authorizeJobAccess($imageJob);

        return response()->json([
            'status'          => $imageJob->status,
            'progress'        => $imageJob->progressPercent(),
            'processed_files' => $imageJob->processed_files,
            'failed_files'    => $imageJob->failed_files,
            'total_files'     => $imageJob->total_files,
            'is_finished'     => $imageJob->isFinished(),
            'download_url'    => $imageJob->status === ImageJob::STATUS_DONE
                ? route('jobs.download', $imageJob->uuid) : null,
        ]);
    }

    public function resultFiles(ImageJob $imageJob)
    {
        $this->authorizeJobAccess($imageJob);

        $files = $imageJob->files()
            ->where('status', 'done')
            ->whereNotNull('result_path')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'original_name', 'result_width', 'result_height', 'result_size', 'result_path']);

        return response()->json([
            'files' => $files->map(fn ($file) => [
                'id' => $file->id,
                'name' => basename((string) $file->result_path),
                'width' => $file->result_width,
                'height' => $file->result_height,
                'size' => $file->result_size,
                'preview_url' => route('jobs.files.preview', ['imageJob' => $imageJob->uuid, 'file' => $file->id]),
            ])->values(),
        ]);
    }

    public function previewResultFile(ImageJob $imageJob, int $file)
    {
        $this->authorizeJobAccess($imageJob);

        $jobFile = $imageJob->files()
            ->whereKey($file)
            ->where('status', 'done')
            ->firstOrFail();

        if (! $jobFile->result_path || ! Storage::disk('local')->exists($jobFile->result_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($jobFile->result_path));
    }

    /** Download ZIP */
    public function download(ImageJob $imageJob)
    {
        $this->authorizeJobAccess($imageJob);

        if ($imageJob->status !== ImageJob::STATUS_DONE || ! $imageJob->result_archive_path) {
            abort(404, dbt('jobs.messages.archive_not_ready'));
        }

        $absPath = Storage::disk('local')->path($imageJob->result_archive_path);

        if (! file_exists($absPath)) {
            abort(404, dbt('jobs.messages.archive_missing'));
        }

        return response()->download($absPath, "bir_{$imageJob->uuid}.zip");
    }

    /** User job history */
    public function history(Request $request)
    {
        $this->middleware('auth');
        $jobs = auth()->user()->imageJobs()->latest()->paginate(20);
        return view('history', compact('jobs'));
    }

    private function authorizeJobAccess(ImageJob $job): void
    {
        $user = auth()->user();
        if ($user && $job->user_id === $user->id) return;
        if ($job->session_id && $job->session_id === session()->getId()) return;
        abort(403);
    }
}
