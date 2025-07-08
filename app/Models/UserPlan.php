<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class UserPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'started_at',
        'expires_at',
        'billing_period',
        'amount_paid',
        'notes'
    ];

    protected $casts = [
        'features' => 'array',
        'started_at' => 'datetime:Y-m-d H:i:s',
        'expires_at' => 'datetime:Y-m-d H:i:s',
        'amount_paid' => 'decimal:2'
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' &&
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->expires_at !== null && $this->expires_at->isPast());
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', Carbon::now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'expired')
              ->orWhere('expires_at', '<', Carbon::now());
        });
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
