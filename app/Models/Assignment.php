<?php

namespace App\Models;

use App\Enums\AssignmentType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Assignment extends Model implements HasMedia
{
    // --- Traits ---

    use HasFactory, InteractsWithMedia, SoftDeletes;

    // --- Properties ---

    protected $guarded = ['id'];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'type' => AssignmentType::class,
        ];
    }

    // --- Accessors & Mutators ---

    protected function formattedDueDate(): Attribute
    {
        return Attribute::get(fn () => $this->due_date?->translatedFormat('l, d M Y H:i'));
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

    public function assignmentPins(): HasMany
    {
        return $this->hasMany(AssignmentPin::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function assignmentTargets(): HasMany
    {
        return $this->hasMany(AssignmentTarget::class);
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'assignment_targets',
            'assignment_id',
            'student_id'
        );
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            StudyGroup::class,
            'assignment_targets',
            'assignment_id',
            'study_group_id'
        );
    }
}
