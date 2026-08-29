<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_goal',
        'target',
        'progres',
        'deadline',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target' => 'decimal:2',
            'progres' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function percentage(): float
    {
        $target = (float) $this->target;
        $progres = (float) $this->progres;

        if ($target <= 0) {
            return 0.0;
        }

        return min(100.0, round(($progres / $target) * 100, 1));
    }

    public function remainingAmount(): float
    {
        $remaining = (float) $this->target - (float) $this->progres;

        return max(0.0, $remaining);
    }

    public function isCompleted(): bool
    {
        return (float) $this->progres >= (float) $this->target;
    }

    public function daysLeft(): ?int
    {
        if (! $this->deadline) {
            return null;
        }

        $now = Carbon::now()->startOfDay();
        $deadline = Carbon::parse($this->deadline)->startOfDay();

        return (int) $now->diffInDays($deadline, false);
    }
}
