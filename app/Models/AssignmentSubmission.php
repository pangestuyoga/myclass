<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AssignmentSubmission extends Model implements HasMedia
{
    // --- Traits ---

    use HasFactory, InteractsWithMedia;

    // --- Properties ---

    protected $guarded = ['id'];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    // --- Accessors & Mutators ---

    protected function formattedSubmittedAt(): Attribute
    {
        return Attribute::get(fn () => $this->submitted_at?->translatedFormat('l, d M Y H:i'));
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

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function studyGroup(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class);
    }
}
