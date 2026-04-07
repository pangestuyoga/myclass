<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
    // --- Traits ---

    use HasFactory, SoftDeletes;

    // --- Properties ---

    protected $guarded = ['id'];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'end_time' => 'datetime',
            'session_number' => 'integer',
            'start_time' => 'datetime',
            'is_sent_to_lecturer' => 'boolean',
        ];
    }

    // --- Accessors & Mutators ---

    protected function formattedDate(): Attribute
    {
        return Attribute::get(fn () => $this->date?->translatedFormat('l, d M Y'));
    }

    protected function formattedStartTime(): Attribute
    {
        return Attribute::get(fn () => $this->start_time?->translatedFormat('H:i'));
    }

    protected function formattedEndTime(): Attribute
    {
        return Attribute::get(fn () => $this->end_time?->translatedFormat('H:i'));
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

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
