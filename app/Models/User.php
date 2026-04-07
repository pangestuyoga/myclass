<?php

namespace App\Models;

use App\Enums\IsActive;
use App\Enums\NotifStyle;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Saade\FilamentFacehash\Concerns\HasFacehashAvatar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    // --- Traits ---

    /** @use HasFactory<UserFactory> */
    use HasFacehashAvatar, HasFactory, HasRoles, Notifiable, SoftDeletes;

    // --- Properties ---

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'is_active' => IsActive::class,
            'password' => 'hashed',
        ];
    }

    // --- Scopes ---

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', IsActive::Active);
    }

    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('is_active', IsActive::Inactive);
    }

    // --- Accessors & Mutators ---

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->student?->full_name
        );
    }

    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->created_at?->translatedFormat('l, d M Y H:i'));
    }

    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->updated_at?->translatedFormat('l, d M Y H:i'));
    }

    // --- Relations ---

    public function changelogs(): BelongsToMany
    {
        return $this->belongsToMany(Changelog::class)->withPivot('read_at')->withTimestamps();
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class)->withDefault([
            'notif_style' => NotifStyle::Cheerful,
            'primary_color' => 'amber',
            'font' => 'Inter',
            'content_width' => 'full',
            'border_radius' => 'lg',
            'top_navigation' => false,
        ]);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    // --- Methods ---

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active === IsActive::Active;
    }
}
