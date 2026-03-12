<?php

namespace App\Models;

use App\Enums\IsActive;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Saade\FilamentFacehash\Concerns\HasFacehashAvatar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFacehashAvatar, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => IsActive::class,
            'password' => 'hashed',
        ];
    }

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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->student?->full_name
        );
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }
}
