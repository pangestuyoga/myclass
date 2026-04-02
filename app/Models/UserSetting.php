<?php

namespace App\Models;

use App\Enums\NotifStyle;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    // --- Properties ---

    protected $guarded = ['id'];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'notif_style' => NotifStyle::class,
            'top_navigation' => 'boolean',
        ];
    }

    // --- Accessors & Mutators ---

    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->created_at->translatedFormat('l, d M Y H:i'));
    }

    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->updated_at?->translatedFormat('l, d M Y H:i'));
    }

    // --- Relations ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
