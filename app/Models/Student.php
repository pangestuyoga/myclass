<?php

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'sex' => Sex::class,
        ];
    }

    #[Scope]
    protected function male(Builder $query): void
    {
        $query->where('sex', Sex::Male);
    }

    #[Scope]
    protected function female(Builder $query): void
    {
        $query->where('sex', Sex::Female);
    }

    public function assignmentPins(): HasMany
    {
        return $this->hasMany(AssignmentPin::class);
    }

    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(
            Assignment::class,
            'assignment_targets',
            'student_id',
            'assignment_id'
        );
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function assignmentTargets(): HasMany
    {
        return $this->hasMany(AssignmentTarget::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function ledStudyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'leader_id');
    }

    public function studyGroupMembers(): HasMany
    {
        return $this->hasMany(StudyGroupMember::class);
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_members');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
