<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($task) {
            if ($task->is_completed && ! $task->getOriginal('is_completed')) {
                $task->completed_at = now();
            } elseif (! $task->is_completed && $task->getOriginal('is_completed')) {
                $task->completed_at = null;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
