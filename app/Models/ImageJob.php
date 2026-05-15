<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ImageJob extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'session_id', 'status', 'name',
        'pipeline', 'output_format', 'output_quality',
        'rename_mode', 'rename_prefix', 'rename_suffix', 'rename_start_number',
        'total_files', 'processed_files', 'failed_files',
        'result_archive_path', 'result_size_bytes',
        'started_at', 'completed_at', 'expires_at',
    ];

    protected $casts = [
        'pipeline'    => 'array',
        'rename_start_number' => 'integer',
        'started_at'  => 'datetime',
        'completed_at'=> 'datetime',
        'expires_at'  => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DONE       = 'done';
    const STATUS_FAILED     = 'failed';
    const STATUS_EXPIRED    = 'expired';

    protected static function booted(): void
    {
        static::creating(function (self $job) {
            if (empty($job->uuid)) {
                $job->uuid = (string) Str::uuid();
            }
        });
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(ImageJobFile::class)->orderBy('sort_order');
    }

    // Scopes
    public function scopePending($q)    { return $q->where('status', self::STATUS_PENDING); }
    public function scopeProcessing($q) { return $q->where('status', self::STATUS_PROCESSING); }
    public function scopeDone($q)       { return $q->where('status', self::STATUS_DONE); }

    // Helpers
    public function progressPercent(): int
    {
        if ($this->total_files === 0) return 0;
        return (int) round(($this->processed_files + $this->failed_files) / $this->total_files * 100);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, [self::STATUS_DONE, self::STATUS_FAILED, self::STATUS_EXPIRED]);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function buildResultFilename(string $originalName, ?int $index = null): string
    {
        $info = pathinfo($originalName);
        $base = $info['filename'];
        $ext  = $this->output_format ?? ($info['extension'] ?? 'jpg');

        if ($this->rename_mode === 'sequence') {
            $number = ($this->rename_start_number ?? 1) + ($index ?? 0);
            $base = str_pad((string) $number, 3, '0', STR_PAD_LEFT);
        }

        return ($this->rename_prefix ?? '') . $base . ($this->rename_suffix ?? '') . '.' . $ext;
    }

    public function localizedStatus(): string
    {
        return self::localizedStatusFor((string) $this->status);
    }

    public static function localizedStatusFor(string $status): string
    {
        $key = 'status.job.' . $status;
        $translated = dbt($key);

        return $translated === 'ui.' . $key
            ? Str::headline($status)
            : $translated;
    }
}
