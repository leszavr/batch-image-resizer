<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    protected $fillable = [
        'user_id', 'name', 'is_global',
        'pipeline', 'output_format', 'output_quality',
        'rename_mode', 'rename_prefix', 'rename_suffix', 'rename_start_number', 'used_count',
    ];

    protected $casts = [
        'pipeline'  => 'array',
        'is_global' => 'boolean',
        'rename_start_number' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeGlobal($q)
    {
        return $q->where('is_global', true);
    }

    public function scopeForUser($q, ?int $userId)
    {
        return $q->where(function ($q) use ($userId) {
            $q->where('is_global', true);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        });
    }
}
