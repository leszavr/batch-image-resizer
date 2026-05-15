<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageJobFile extends Model
{
    protected $fillable = [
        'image_job_id', 'original_name', 'original_path', 'result_path',
        'original_mime', 'original_size', 'result_size',
        'original_width', 'original_height', 'result_width', 'result_height',
        'status', 'error_message', 'sort_order',
    ];

    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DONE       = 'done';
    const STATUS_FAILED     = 'failed';
    const STATUS_SKIPPED    = 'skipped';

    public function job()
    {
        return $this->belongsTo(ImageJob::class, 'image_job_id');
    }

    public function getResultFilename(): string
    {
        return $this->job->buildResultFilename($this->original_name);
    }

    public function originalSizeFormatted(): string
    {
        return $this->formatBytes($this->original_size);
    }

    public function resultSizeFormatted(): string
    {
        return $this->formatBytes($this->result_size);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}
