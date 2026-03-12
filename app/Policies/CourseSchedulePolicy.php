<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CourseSchedule;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CourseSchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CourseSchedule');
    }

    public function view(AuthUser $authUser, CourseSchedule $courseSchedule): bool
    {
        return $authUser->can('View:CourseSchedule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CourseSchedule');
    }

    public function update(AuthUser $authUser, CourseSchedule $courseSchedule): bool
    {
        return $authUser->can('Update:CourseSchedule');
    }

    public function delete(AuthUser $authUser, CourseSchedule $courseSchedule): bool
    {
        return $authUser->can('Delete:CourseSchedule');
    }

    public function restore(AuthUser $authUser, CourseSchedule $courseSchedule): bool
    {
        return $authUser->can('Restore:CourseSchedule');
    }

    public function forceDelete(AuthUser $authUser, CourseSchedule $courseSchedule): bool
    {
        return $authUser->can('ForceDelete:CourseSchedule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CourseSchedule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CourseSchedule');
    }

    public function replicate(AuthUser $authUser, CourseSchedule $courseSchedule): bool
    {
        return $authUser->can('Replicate:CourseSchedule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CourseSchedule');
    }
}
