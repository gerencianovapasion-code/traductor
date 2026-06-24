<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'locale',
        'default_source_lang',
        'default_target_lang',
        'affiliate_code',
        'referred_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->affiliate_code)) {
                $user->affiliate_code = self::generateAffiliateCode();
            }
        });
    }

    public static function generateAffiliateCode(): string
    {
        do {
            $code = Str::lower(Str::random(8));
        } while (self::where('affiliate_code', $code)->exists());

        return $code;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ---- Relationships ----

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'affiliate_id');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function translationSessions(): HasMany
    {
        return $this->hasMany(TranslationSession::class);
    }

    // ---- Membership helpers ----

    public function currentPlan(): Plan
    {
        $sub = $this->activeSubscription;

        if ($sub && $sub->plan && (! $sub->ends_at || $sub->ends_at->isFuture())) {
            return $sub->plan;
        }

        return Plan::freePlan();
    }

    public function planLevel(): int
    {
        return $this->currentPlan()->level;
    }

    /** Approved commission balance available to withdraw (not yet tied to a payout), in cents. */
    public function availableBalanceCents(): int
    {
        return (int) $this->commissions()
            ->where('status', 'approved')
            ->whereNull('payout_id')
            ->sum('amount_cents');
    }
}
