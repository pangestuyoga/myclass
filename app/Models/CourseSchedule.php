<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSchedule extends Model
{
    // --- Traits ---

    use HasFactory, SoftDeletes;

    // --- Properties ---

    protected $guarded = ['id'];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'end_time' => 'datetime:H:i',
            'start_time' => 'datetime:H:i',
        ];
    }

    // --- Accessors & Mutators ---

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
        return Attribute::get(fn () => $this->created_at->translatedFormat('l, d M Y H:i'));
    }

    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->updated_at?->translatedFormat('l, d M Y H:i'));
    }

    // --- Relations ---

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
