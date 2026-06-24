<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'code', 'name', 'native_name', 'flag', 'speech_code',
        'can_listen', 'can_speak', 'ui', 'is_active', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'can_listen' => 'boolean',
            'can_speak' => 'boolean',
            'ui' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort')->orderBy('name');
    }

    public function scopeUi($query)
    {
        return $query->active()->where('ui', true);
    }

    public function label(): string
    {
        return trim(($this->flag ? $this->flag.' ' : '').($this->native_name ?: $this->name));
    }
}
