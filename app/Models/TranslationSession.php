<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationSession extends Model
{
    protected $fillable = [
        'user_id', 'source_lang', 'target_lang', 'engine', 'seconds', 'characters',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
