<?php

namespace App\Models;

use App\Enums\AssignmentType;
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
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'type' => AssignmentType::class,
        ];
    }

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
