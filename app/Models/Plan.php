<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'slug', 'name', 'level', 'price_cents', 'currency', 'interval',
        'minutes_limit', 'engine', 'allow_system_audio', 'ads', 'features',
        'is_active', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'allow_system_audio' => 'boolean',
            'ads' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public static function freePlan(): self
    {
        return static::where('slug', 'free')->first()
            ?? new self([
                'slug' => 'free', 'name' => 'Free', 'level' => 1,
                'price_cents' => 0, 'currency' => 'EUR', 'interval' => 'month',
                'minutes_limit' => 30, 'engine' => 'browser',
                'allow_system_audio' => false, 'ads' => true,
            ]);
    }

    public function isFree(): bool
    {
        return $this->level <= 1 || $this->price_cents === 0;
    }

    public function priceFormatted(): string
    {
        return number_format($this->price_cents / 100, 2, ',', '.').' '.$this->currency;
    }
}
