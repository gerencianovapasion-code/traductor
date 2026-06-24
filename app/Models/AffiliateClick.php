<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateClick extends Model
{
    protected $fillable = [
        'affiliate_code', 'affiliate_id', 'ip_address', 'user_agent',
        'referer', 'landing', 'converted',
    ];

    protected function casts(): array
    {
        return ['converted' => 'boolean'];
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
}
