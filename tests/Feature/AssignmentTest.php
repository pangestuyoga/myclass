<?php

use App\Enums\AssignmentType;
use App\Enums\RoleEnum;
use App\Filament\Resources\Learning\Assignments\Pages\CreateAssignment;
use App\Filament\Resources\Learning\Assignments\Pages\ListAssignments;
use App\Filament\Resources\Learning\Assignments\Pages\SubmissionDetailPage;
use App\Filament\Resources\Learning\Assignments\Pages\SubmitAssignmentPage;
use App\Models\Assignment;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        'ViewAny:Assignment',
        'View:Assignment',
        'Create:Assignment',
        'Update:Assignment',
        'Delete:Assignment',
        'View:CourseSchedule',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }

    Storage::fake('public');
    $this->currentSemester = app(GeneralSettings::class)->current_semester;
});

describe('Assignment Authorization', function () {
    it('restricts actions to Developer and Kosma, while allowing students to view and submit', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['ViewAny:Assignment', 'Create:Assignment', 'View:CourseSchedule']);

        $studentUser = User::factory()->create();
        $studentUser->assignRole(RoleEnum::Student);
        $studentUser->givePermissionTo(['ViewAny:Assignment', 'View:CourseSchedule']);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $this->actingAs($developer);
        Livewire::test(ListAssignments::class)
            ->assertActionVisible('createAssignment');

        $this->actingAs($studentUser);
        Livewire::test(ListAssignments::class)
            ->assertActionHidden('createAssignment');
    });
});

describe('Assignment Model', function () {
    it('has relationships and supports soft deletes', function () {
        $assignment = Assignment::factory()->create();

        expect($assignment->course)->toBeInstanceOf(Course::class);
        expect($assignment->assignmentSubmissions())->toBeInstanceOf(HasMany::class);

        $assignment->delete();
        expect($assignment->refresh()->trashed())->toBeTrue();
    });
});

describe('Assignment CRUD and Rules', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Assignment', 'Create:Assignment', 'Update:Assignment', 'Delete:Assignment', 'View:CourseSchedule']);
        $this->actingAs($user);
    });

    it('can create an assignment', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $students = Student::factory()->count(2)->create();

        Livewire::test(CreateAssignment::class)
            ->fillForm([
                'title' => 'New Test Assignment',
                'course_id' => $course->id,
                'due_date' => now()->addDays(7)->toDateTimeString(),
                'type' => AssignmentType::Individual->value,
                'student_ids' => $students->pluck('id')->toArray(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('assignments', [
            'title' => 'New Test Assignment',
            'course_id' => $course->id,
        ]);
    });

    it('enforces one assignment per session rule', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $session = ClassSession::factory()->create(['course_id' => $course->id]);

        Assignment::factory()->create([
            'course_id' => $course->id,
            'class_session_id' => $session->id,
            'type' => AssignmentType::Individual,
        ]);

        Livewire::test(CreateAssignment::class)
            ->set('data.course_id', $course->id)
            ->assertFormSet(['class_session_id' => null]);
    });
});

describe('Assignment Submission Logic', function () {
    it('allows individual submission', function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Student);
        $user->givePermissionTo(['ViewAny:Assignment', 'View:CourseSchedule']);
        $student = Student::factory()->create(['user_id' => $user->id]);

        $assignment = Assignment::factory()->create([
            'type' => AssignmentType::Individual,
            'due_date' => now()->addDays(1),
        ]);
        $assignment->students()->attach($student->id);
        $assignment->course->update(['semester' => $this->currentSemester]);

        $file = UploadedFile::fake()->create('submission.pdf', 100, 'application/pdf');

        $this->actingAs($user);
        Livewire::test(SubmitAssignmentPage::class, ['record' => $assignment])
            ->assertSuccessful()
            ->fillForm([
                'file' => $file,
            ])
            ->call('submit')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ]);
    });

    it('only allows group leader to submit group assignments', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);

        $leaderUser = User::factory()->create();
        $leaderUser->assignRole(RoleEnum::Student);
        $leaderUser->givePermissionTo(['ViewAny:Assignment', 'View:CourseSchedule']);
        $leader = Student::factory()->create(['user_id' => $leaderUser->id]);

        $memberUser = User::factory()->create();
        $memberUser->assignRole(RoleEnum::Student);
        $memberUser->givePermissionTo(['ViewAny:Assignment', 'View:CourseSchedule']);
        $member = Student::factory()->create(['user_id' => $memberUser->id]);

        $group = StudyGroup::factory()->create([
            'leader_id' => $leader->id,
        ]);
        $group->courses()->attach($course->id);
        $group->students()->attach($member->id);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'type' => AssignmentType::Group,
            'due_date' => now()->addDays(1),
        ]);
        $assignment->studyGroups()->attach($group->id);

        // Test Leader can submit
        $this->actingAs($leaderUser);
        Livewire::test(SubmitAssignmentPage::class, ['record' => $assignment])
            ->assertSuccessful()
            ->assertSet('canSubmit', true);

        // Test Member cannot submit
        $this->actingAs($memberUser);
        Livewire::test(SubmitAssignmentPage::class, ['record' => $assignment])
            ->assertSuccessful()
            ->assertSet('canSubmit', false);
    });
});

describe('Assignment Pins and Interactions', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Student);
        $user->givePermissionTo(['ViewAny:Assignment', 'View:CourseSchedule']);
        $this->studentProfile = Student::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can pin and unpin an assignment', function () {
        $assignment = Assignment::factory()->create();
        $assignment->course->update(['semester' => $this->currentSemester]);
        $assignment->students()->attach($this->studentProfile->id);

        Livewire::test(ListAssignments::class)
            ->callAction('pin', [], ['record' => $assignment->id]);

        $this->assertDatabaseHas('assignment_pins', [
            'student_id' => $this->studentProfile->id,
            'assignment_id' => $assignment->id,
        ]);

        Livewire::test(ListAssignments::class)
            ->callAction('pin', [], ['record' => $assignment->id]);

        $this->assertDatabaseMissing('assignment_pins', [
            'student_id' => $this->studentProfile->id,
            'assignment_id' => $assignment->id,
        ]);
    });
});

describe('Assignment Search', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Student);
        $user->givePermissionTo(['ViewAny:Assignment', 'View:CourseSchedule']);
        $this->studentProfile = Student::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can search assignments by title', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);

        $assignment1 = Assignment::factory()->create([
            'title' => 'Tugas Matematika 1',
            'course_id' => $course->id,
            'due_date' => now()->addDays(1),
        ]);
        $assignment2 = Assignment::factory()->create([
            'title' => 'Tugas Biologi 2',
            'course_id' => $course->id,
            'due_date' => now()->addDays(1),
        ]);

        $assignment1->students()->attach($this->studentProfile->id);
        $assignment2->students()->attach($this->studentProfile->id);

        Livewire::test(ListAssignments::class)
            ->set('search', 'Matematika')
            ->assertSee('Tugas Matematika 1')
            ->assertDontSee('Tugas Biologi 2');
    });

    it('can search assignments by course name', function () {
        $course1 = Course::factory()->create(['name' => 'Fisika Dasar', 'semester' => $this->currentSemester]);
        $course2 = Course::factory()->create(['name' => 'Sejarah Indonesia', 'semester' => $this->currentSemester]);

        $assignment1 = Assignment::factory()->create([
            'title' => 'Laporan Praktikum',
            'course_id' => $course1->id,
            'due_date' => now()->addDays(1),
        ]);
        $assignment2 = Assignment::factory()->create([
            'title' => 'Makalah',
            'course_id' => $course2->id,
            'due_date' => now()->addDays(1),
        ]);

        $assignment1->students()->attach($this->studentProfile->id);
        $assignment2->students()->attach($this->studentProfile->id);

        Livewire::test(ListAssignments::class)
            ->set('search', 'Fisika')
            ->assertSee('Laporan Praktikum')
            ->assertSee('Fisika Dasar')
            ->assertDontSee('Makalah')
            ->assertDontSee('Sejarah Indonesia');
    });

    it('shows empty state when no assignments found with search', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $assignment = Assignment::factory()->create([
            'title' => 'Tugas Wajib',
            'course_id' => $course->id,
            'due_date' => now()->addDays(1),
        ]);
        $assignment->students()->attach($this->studentProfile->id);

        Livewire::test(ListAssignments::class)
            ->set('search', 'Random string yang ga ada')
            ->assertDontSee('Tugas Wajib')
            ->assertSee('Hore, Belum Ada Tugas!');
    });

    it('can filter assignments by course', function () {
        $course1 = Course::factory()->create(['name' => 'Matematika', 'semester' => $this->currentSemester]);
        $course2 = Course::factory()->create(['name' => 'Fisika', 'semester' => $this->currentSemester]);

        $assignment1 = Assignment::factory()->create([
            'title' => 'Tugas Mat',
            'course_id' => $course1->id,
            'due_date' => now()->addDays(1),
        ]);
        $assignment2 = Assignment::factory()->create([
            'title' => 'Tugas Fis',
            'course_id' => $course2->id,
            'due_date' => now()->addDays(1),
        ]);

        $assignment1->students()->attach($this->studentProfile->id);
        $assignment2->students()->attach($this->studentProfile->id);

        Livewire::test(ListAssignments::class)
            ->set('course_id', $course1->id)
            ->assertSee('Tugas Mat')
            ->assertDontSee('Tugas Fis');
    });
});

describe('Assignment Sharing', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Student);
        $user->givePermissionTo(['ViewAny:Assignment', 'View:CourseSchedule']);
        $this->studentProfile = Student::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can share assignment information', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $assignment = Assignment::factory()->create(['course_id' => $course->id]);
        $assignment->students()->attach($this->studentProfile->id);

        Livewire::test(ListAssignments::class)
            ->callAction('shareAssignment', [], ['record' => $assignment->id])
            ->assertHasNoActionErrors();
    });
});

describe('Assignment Details and Preview', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Assignment', 'View:Assignment', 'View:CourseSchedule']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can view submission detail page', function () {
        $assignment = Assignment::factory()->create();
        $assignment->course->update(['semester' => $this->currentSemester]);

        Livewire::test(SubmissionDetailPage::class, ['record' => $assignment])
            ->assertSuccessful()
            ->assertSee($assignment->title);
    });
});
