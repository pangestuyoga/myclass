<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function isLeader(Student $student): bool
    {
        return $this->leader_id === $student->id;
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function assignmentTargets(): HasMany
    {
        return $this->hasMany(AssignmentTarget::class);
    }

    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(
            Assignment::class,
            'assignment_targets',
            'study_group_id',
            'assignment_id'
        );
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'study_group_courses');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(StudyGroupMember::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'study_group_members');
    }

    public static function getBusyStudentIdsForCourses(array $courseIds, ?int $excludeGroupId = null): array
    {
        $busyMemberIds = StudyGroupMember::whereHas(
            'studyGroup',
            function ($q) use ($courseIds, $excludeGroupId) {
                if ($excludeGroupId) {
                    $q->where('study_groups.id', '!=', $excludeGroupId);
                }
                $q->whereHas('courses', function ($cq) use ($courseIds) {
                    $cq->whereIn('courses.id', $courseIds);
                });
            }
        )->pluck('student_id')->toArray();

        $busyLeaderIds = static::query()
            ->when($excludeGroupId, fn ($q) => $q->where('id', '!=', $excludeGroupId))
            ->whereHas('courses', function ($cq) use ($courseIds) {
                $cq->whereIn('courses.id', $courseIds);
            })
            ->pluck('leader_id')
            ->toArray();

        return array_unique(array_filter(array_merge($busyMemberIds, $busyLeaderIds)));
    }
}
