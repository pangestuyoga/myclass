<?php

use App\Enums\RoleEnum;
use App\Filament\Resources\Learning\Attendances\Pages\ManageAttendances;
use App\Filament\Resources\Learning\ClassSessions\Pages\ListCourseSessions;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Student;
use App\Models\User;
use App\Settings\GeneralSettings;
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
        'ViewAny:Attendance',
        'Create:Attendance',
        'Delete:Attendance',
        'ViewAny:Course',
        'ViewAny:ClassSession',
        'View:ClassSession',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }

    $this->currentSemester = app(GeneralSettings::class)->current_semester;
});

describe('Attendance Authorization', function () {
    it('allows all roles to view, but only specific actions are allowed for each role', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole(RoleEnum::Student);
        $studentUser->givePermissionTo(['ViewAny:Attendance']);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $this->actingAs($studentUser);
        Livewire::test(ManageAttendances::class)
            ->assertSuccessful();
    });

    it('allows non-student roles like Developer to access page safely', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['ViewAny:Attendance']);

        $this->actingAs($developer);
        Livewire::test(ManageAttendances::class)
            ->assertSuccessful();
    });
});

describe('Attendance Model', function () {
    it('has necessary relationships', function () {
        $attendance = Attendance::factory()->create();

        expect($attendance->student)->toBeInstanceOf(Student::class);
        expect($attendance->classSession)->toBeInstanceOf(ClassSession::class);
        expect($attendance->courseSchedule)->toBeInstanceOf(CourseSchedule::class);
    });
});

describe('Attendance Logic (Presensi)', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->user->assignRole(RoleEnum::Student);
        $this->user->givePermissionTo(['ViewAny:Attendance']);
        $this->student = Student::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
    });

    it('can perform attendance during class session', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);

        // Define day and time
        $now = now();
        $session = ClassSession::factory()->create([
            'course_id' => $course->id,
            'date' => $now->toDateString(),
            'start_time' => $now->copy()->subMinutes(10), // started 10 minutes ago
            'end_time' => $now->copy()->addMinutes(110),
        ]);

        // Need a compatible schedule
        $schedule = CourseSchedule::factory()->create([
            'course_id' => $course->id,
            'day_of_week' => $now->dayOfWeekIso,
        ]);

        Livewire::test(ManageAttendances::class)
            ->call('attend', $session->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'class_session_id' => $session->id,
            'course_schedule_id' => $schedule->id,
        ]);
    });

    it('prevents attendance before class starts', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $now = now();
        $session = ClassSession::factory()->create([
            'course_id' => $course->id,
            'date' => $now->toDateString(),
            'start_time' => $now->copy()->addHour(), // starts in 1 hour
        ]);

        Livewire::test(ManageAttendances::class)
            ->call('attend', $session->id);

        $this->assertDatabaseMissing('attendances', [
            'student_id' => $this->student->id,
            'class_session_id' => $session->id,
        ]);
    });

    it('prevents double attendance for same session', function () {
        $attendance = Attendance::factory()->create([
            'student_id' => $this->student->id,
        ]);

        Livewire::test(ManageAttendances::class)
            ->call('attend', $attendance->class_session_id);

        expect(Attendance::where('student_id', $this->student->id)
            ->where('class_session_id', $attendance->class_session_id)
            ->count())->toBe(1);
    });
});

describe('Attendance History Table', function () {
    it('can filter results by course in history table', function () {
        $this->user = User::factory()->create();
        $this->user->assignRole(RoleEnum::Student);
        $this->user->givePermissionTo(['ViewAny:Attendance']);
        $student = Student::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);

        $course1 = Course::factory()->create(['semester' => $this->currentSemester]);
        $course2 = Course::factory()->create(['semester' => $this->currentSemester]);

        $schedule1 = CourseSchedule::factory()->create(['course_id' => $course1->id]);
        $schedule2 = CourseSchedule::factory()->create(['course_id' => $course2->id]);

        $att1 = Attendance::factory()->create([
            'student_id' => $student->id,
            'course_schedule_id' => $schedule1->id,
            'date' => now()->toDateString(),
        ]);
        $att2 = Attendance::factory()->create([
            'student_id' => $student->id,
            'course_schedule_id' => $schedule2->id,
            'date' => now()->subDay()->toDateString(),
        ]);

        Livewire::test(ManageAttendances::class)
            ->filterTable('course_id', $course1->id)
            ->assertCanSeeTableRecords([$att1])
            ->assertCanNotSeeTableRecords([$att2]);
    });
});

describe('Attendance Delivery Status', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->user->assignRole(RoleEnum::Student);
        $this->user->givePermissionTo(['ViewAny:Attendance']);
        $this->student = Student::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('allows attendance for past sessions if not yet sent to lecturer', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $yesterday = now()->subDay();

        $session = ClassSession::factory()->create([
            'course_id' => $course->id,
            'date' => $yesterday->toDateString(),
            'start_time' => $yesterday->copy()->setTime(8, 0),
            'end_time' => $yesterday->copy()->setTime(10, 0),
            'is_sent_to_lecturer' => false,
        ]);

        $schedule = CourseSchedule::factory()->create([
            'course_id' => $course->id,
            'day_of_week' => $yesterday->dayOfWeekIso,
        ]);

        Livewire::test(ManageAttendances::class)
            ->call('attend', $session->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'class_session_id' => $session->id,
        ]);
    });

    it('prevents attendance if session is already sent to lecturer', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $now = now();

        $session = ClassSession::factory()->create([
            'course_id' => $course->id,
            'date' => $now->toDateString(),
            'start_time' => $now->copy()->subHours(1),
            'is_sent_to_lecturer' => true,
        ]);

        Livewire::test(ManageAttendances::class)
            ->call('attend', $session->id);

        $this->assertDatabaseMissing('attendances', [
            'student_id' => $this->student->id,
            'class_session_id' => $session->id,
        ]);
    });

    it('allows Kosma/Developer to toggle delivery status', function () {
        $kosmaUser = User::factory()->create();
        $kosmaUser->assignRole(RoleEnum::Kosma);
        $kosmaUser->givePermissionTo(['ViewAny:Course', 'ViewAny:ClassSession', 'ViewAny:Attendance']);
        $this->actingAs($kosmaUser);

        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create([
            'course_id' => $course->id,
            'is_sent_to_lecturer' => false,
        ]);

        // Using ListCourseSessions page to trigger the action
        Livewire::test(ListCourseSessions::class, [
            'course' => $course,
        ])
            ->callAction('markAsSentAction', [], ['record' => $session->id])
            ->assertHasNoErrors();

        expect($session->fresh()->is_sent_to_lecturer)->toBeTrue();

        Livewire::test(ListCourseSessions::class, [
            'course' => $course,
        ])
            ->callAction('markAsSentAction', [], ['record' => $session->id])
            ->assertHasNoErrors();

        expect($session->fresh()->is_sent_to_lecturer)->toBeFalse();
    });
});
