<?php

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    // --- Traits ---

    use HasFactory, SoftDeletes;

    // --- Properties ---

    protected $guarded = ['id'];

    // --- Casts ---

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'sex' => Sex::class,
        ];
    }

    // --- Scopes ---

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

    // --- Accessors & Mutators ---

    protected function formattedDateOfBirth(): Attribute
    {
        return Attribute::get(fn () => $this->date_of_birth?->translatedFormat('l, d M Y'));
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
