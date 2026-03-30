<?php

namespace App\Models;

use App\Enums\NotifStyle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'notif_style' => NotifStyle::class,
            'top_navigation' => 'boolean',
        ];
    }
}
