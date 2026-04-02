<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    // --- Traits ---

    use HasFactory;

    // --- Properties ---

    protected $guarded = ['id'];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
            'date' => 'date',
        ];
    }

    // --- Accessors & Mutators ---

    protected function formattedDate(): Attribute
    {
        return Attribute::get(fn () => $this->date?->translatedFormat('l, d M Y'));
    }

    protected function formattedAttendedAt(): Attribute
    {
        return Attribute::get(fn () => $this->attended_at?->translatedFormat('l, d M Y H:i'));
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

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function courseSchedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
