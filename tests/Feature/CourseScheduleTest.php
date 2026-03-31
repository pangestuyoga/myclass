<?php

use App\Enums\RoleEnum;
use App\Filament\Pages\Information\CourseSchedule\Index as CourseSchedulePage;
use App\Models\Course;
use App\Models\CourseSchedule;
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
        'View:CourseSchedule',
        'Create:CourseSchedule',
        'Update:CourseSchedule',
        'Delete:CourseSchedule',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }
});

describe('Course Schedule Authorization', function () {
    it('allows all roles to view the page but restricts actions to Developer and Kosma', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['View:CourseSchedule', 'Create:CourseSchedule']);

        $kosma = User::factory()->create();
        $kosma->assignRole(RoleEnum::Kosma);
        $kosma->givePermissionTo(['View:CourseSchedule', 'Create:CourseSchedule']);

        $regularStudent = User::factory()->create();
        $regularStudent->assignRole(RoleEnum::Student);
        $regularStudent->givePermissionTo('View:CourseSchedule');

        // All can view the page
        $this->actingAs($developer);
        Livewire::test(CourseSchedulePage::class)->assertSuccessful();

        $this->actingAs($kosma);
        Livewire::test(CourseSchedulePage::class)->assertSuccessful();

        $this->actingAs($regularStudent);
        Livewire::test(CourseSchedulePage::class)->assertSuccessful();

        // Only Developer and Kosma can see the Create action
        $this->actingAs($developer);
        Livewire::test(CourseSchedulePage::class)->assertActionVisible('createSchedule');

        $this->actingAs($kosma);
        Livewire::test(CourseSchedulePage::class)->assertActionVisible('createSchedule');

        $this->actingAs($regularStudent);
        Livewire::test(CourseSchedulePage::class)->assertActionHidden('createSchedule');
    });
});

describe('Course Schedule Model', function () {
    it('has a relationship with Course', function () {
        $schedule = CourseSchedule::factory()->create();
        expect($schedule->course)->toBeInstanceOf(Course::class);
    });

    it('supports Soft Deletes', function () {
        $schedule = CourseSchedule::factory()->create();
        $schedule->delete();

        expect($schedule->trashed())->toBeTrue();
        expect(CourseSchedule::find($schedule->id))->toBeNull();
    });
});

describe('Course Schedule CRUD via Custom Actions', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['View:CourseSchedule', 'Create:CourseSchedule', 'Update:CourseSchedule', 'Delete:CourseSchedule']);
        $this->actingAs($user);

        // Ensure semester matches settings for visibility
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can create a new schedule', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);

        Livewire::test(CourseSchedulePage::class)
            ->callAction('createSchedule', [
                'course_id' => $course->id,
                'day_of_week' => 1, // Senin
                'start_time' => '10:00',
                'end_time' => '12:00',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('course_schedules', [
            'course_id' => $course->id,
            'day_of_week' => 1,
            'start_time' => '10:00',
            'end_time' => '12:00',
        ]);
    });

    it('can update an existing schedule', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $schedule = CourseSchedule::factory()->create([
            'course_id' => $course->id,
            'day_of_week' => 2, // Selasa
        ]);

        Livewire::test(CourseSchedulePage::class)
            ->callAction('editSchedule', [
                'day_of_week' => 3, // Rabu
            ], ['schedule' => $schedule->id])
            ->assertHasNoActionErrors();

        expect($schedule->refresh()->day_of_week)->toBe(3);
    });

    it('can delete a schedule', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $schedule = CourseSchedule::factory()->create(['course_id' => $course->id]);

        Livewire::test(CourseSchedulePage::class)
            ->callAction('deleteSchedule', [], ['schedule' => $schedule->id])
            ->assertHasNoActionErrors();

        expect(CourseSchedule::find($schedule->id))->toBeNull();
    });
});

describe('Search and Search Filtering', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['View:CourseSchedule']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can search schedules by course name or lecturer', function () {
        $targetCourse = Course::factory()->create([
            'name' => 'Specific Course Name',
            'lecturer' => 'Specific Lecturer',
            'semester' => $this->currentSemester,
        ]);
        $targetSchedule = CourseSchedule::factory()->create(['course_id' => $targetCourse->id]);

        $otherCourse = Course::factory()->create([
            'name' => 'Other Course Name',
            'semester' => $this->currentSemester,
        ]);
        $otherSchedule = CourseSchedule::factory()->create(['course_id' => $otherCourse->id]);

        // Search by course name
        Livewire::test(CourseSchedulePage::class)
            ->set('search', 'Specific Course Name')
            ->assertSet('search', 'Specific Course Name');

        // Since it's a custom page, we check visibility within the computed property or the view
        // The schedules are grouped by day name (translated)
        $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $dayName = $dayNames[$targetSchedule->day_of_week];

        Livewire::test(CourseSchedulePage::class)
            ->set('search', 'Specific Course Name')
            ->assertSee($targetCourse->name)
            ->assertDontSee($otherCourse->name);

        // Search by lecturer
        Livewire::test(CourseSchedulePage::class)
            ->set('search', 'Specific Lecturer')
            ->assertSee($targetCourse->name)
            ->assertDontSee($otherCourse->name);
    });

    it('only shows schedules for the current semester', function () {
        $otherSemester = $this->currentSemester + 1;
        $otherCourse = Course::factory()->create(['name' => 'Other Semester Course', 'semester' => $otherSemester]);
        $otherSchedule = CourseSchedule::factory()->create(['course_id' => $otherCourse->id]);

        Livewire::test(CourseSchedulePage::class)
            ->assertDontSee($otherCourse->name);
    });
});
