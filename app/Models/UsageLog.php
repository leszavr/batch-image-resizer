<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'action', 'files_count',
        'credits_used', 'image_job_id', 'ip_address', 'metadata',
    ];

    protected $casts = [
        'metadata'    => 'array',
        'created_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imageJob()
    {
        return $this->belongsTo(ImageJob::class);
    }

    public static function record(string $action, array $data = []): self
    {
        return static::create(array_merge(['action' => $action], $data));
    }
}
