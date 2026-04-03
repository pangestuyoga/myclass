<?php

use App\Enums\RoleEnum;
use App\Filament\Resources\Learning\ClassSessions\Pages\ListCourseSessions;
use App\Filament\Resources\Learning\ClassSessions\Pages\ManageClassSessions;
use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Student;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Setup necessary roles and permissions
 */
beforeEach(function () {
    Role::findOrCreate(RoleEnum::Developer->value);
    Role::findOrCreate(RoleEnum::Kosma->value);
    Role::findOrCreate(RoleEnum::Student->value);

    $permissions = [
        'ViewAny:ClassSession',
        'View:ClassSession',
        'Create:ClassSession',
        'Update:ClassSession',
        'Delete:ClassSession',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }
});

describe('Class Session Authorization', function () {
    it('restricts actions to Developer and Kosma only, while allowing all to view', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['ViewAny:ClassSession', 'View:ClassSession', 'Create:ClassSession']);

        $kosma = User::factory()->create();
        $kosma->assignRole(RoleEnum::Kosma);
        $kosma->givePermissionTo(['ViewAny:ClassSession', 'View:ClassSession', 'Create:ClassSession']);

        $regularStudent = User::factory()->create();
        $regularStudent->assignRole(RoleEnum::Student);
        $regularStudent->givePermissionTo(['ViewAny:ClassSession', 'View:ClassSession']);

        $course = Course::factory()->create(['semester' => app(GeneralSettings::class)->current_semester]);

        // All can view the list (dashboard)
        $this->actingAs($developer);
        Livewire::test(ManageClassSessions::class)->assertSuccessful();

        $this->actingAs($kosma);
        Livewire::test(ManageClassSessions::class)->assertSuccessful();

        $this->actingAs($regularStudent);
        Livewire::test(ManageClassSessions::class)->assertSuccessful();

        // Check actions on ListCourseSessions page
        $this->actingAs($developer);
        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->assertActionVisible('generateSessions');

        $this->actingAs($kosma);
        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->assertActionVisible('generateSessions');

        $this->actingAs($regularStudent);
        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->assertActionHidden('generateSessions');
    });
});

describe('Class Session Model', function () {
    it('has necessary relationships', function () {
        $session = ClassSession::factory()->create();

        expect($session->course)->toBeInstanceOf(Course::class);
        expect($session->attendances())->toBeInstanceOf(HasMany::class);
        expect($session->materials())->toBeInstanceOf(HasMany::class);
        expect($session->assignments())->toBeInstanceOf(HasMany::class);
    });

    it('supports Soft Deletes', function () {
        $session = ClassSession::factory()->create();
        $session->delete();

        expect($session->trashed())->toBeTrue();
        expect(ClassSession::find($session->id))->toBeNull();
    });
});

describe('Class Session Special Features', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:ClassSession', 'View:ClassSession', 'Update:ClassSession', 'Create:ClassSession', 'Delete:ClassSession']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can generate sessions automatically', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);

        // Need a schedule to generate sessions
        CourseSchedule::factory()->create([
            'course_id' => $course->id,
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);

        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->callAction('generateSessions', [
                'total_sessions' => 2,
                'start_date' => now()->toDateString(),
            ])
            ->assertHasNoActionErrors();

        expect($course->classSessions()->count())->toBe(2);

        $this->assertDatabaseHas('class_sessions', [
            'course_id' => $course->id,
            'session_number' => 1,
            'start_time' => now()->format('Y-m-d').' 08:00:00',
            'end_time' => now()->format('Y-m-d').' 10:00:00',
        ]);
    });

    it('can update a session', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create(['course_id' => $course->id, 'session_number' => 1]);

        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->callAction('editSession', [
                'session_number' => 5,
                'date' => now()->toDateString(),
                'start_time' => '13:00',
                'end_time' => '15:00',
            ], ['session' => $session->id])
            ->assertHasNoActionErrors();

        expect($session->refresh()->session_number)->toBe(5);
    });

    it('can share attendance information', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create(['course_id' => $course->id]);

        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->callAction('shareAttendance', [], ['session' => $session->id])
            ->assertHasNoActionErrors();
    });

    it('can show modals for attendance, materials and assignments', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create(['course_id' => $course->id]);

        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->assertActionVisible('viewAttendance')
            ->assertActionVisible('viewMaterials')
            ->assertActionVisible('viewAssignments');
    });

    it('shows student submissions in the assignment modal', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create(['course_id' => $course->id]);
        $assignment = Assignment::factory()->create([
            'title' => 'Test Assignment Label',
            'class_session_id' => $session->id,
            'course_id' => $course->id,
        ]);

        $student = Student::factory()->create(['full_name' => 'John Doe', 'student_number' => '1234567890']);
        AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'submitted_at' => now(),
        ]);

        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->mountAction('viewAssignments', ['session' => $session->id])
            ->assertActionMounted('viewAssignments')
            ->assertSuccessful();
    });

    it('can delete a session', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create(['course_id' => $course->id]);

        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->callAction('deleteSession', [], ['session' => $session->id])
            ->assertHasNoActionErrors();

        expect($session->refresh()->trashed())->toBeTrue();
    });

    it('shows attendance in the attendance modal', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create(['course_id' => $course->id]);

        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->mountAction('viewAttendance', ['session' => $session->id])
            ->assertActionMounted('viewAttendance')
            ->assertSuccessful();
    });

    it('displays dynamic empty states based on system notification style', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);

        // Test ManageClassSessions empty states
        Livewire::test(ManageClassSessions::class)
            ->assertSee(SystemNotification::getByKey('labels.empty_today_sessions.title'))
            ->assertSee(SystemNotification::getByKey('labels.empty_today_sessions.description'));

        // Test ListCourseSessions empty states
        Livewire::test(ListCourseSessions::class, ['course' => $course])
            ->assertSee(SystemNotification::getByKey('labels.empty_course_sessions.title'))
            ->assertSee(SystemNotification::getByKey('labels.empty_course_sessions.description'));
    });
});

describe('Search and Dashboard Filter', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:ClassSession']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can search courses in the session dashboard', function () {
        $target = Course::factory()->create(['name' => 'Session Specific Course', 'semester' => $this->currentSemester]);
        $other = Course::factory()->create(['name' => 'Other Course', 'semester' => $this->currentSemester]);

        Livewire::test(ManageClassSessions::class)
            ->set('search', 'Session Specific Course')
            ->assertSee($target->name)
            ->assertDontSee($other->name);
    });
});
