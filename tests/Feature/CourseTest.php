<?php

use App\Enums\RoleEnum;
use App\Filament\Resources\Master\Courses\Pages\ManageCourses;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'ViewAny:Course',
        'Create:Course',
        'Update:Course',
        'Delete:Course',
        'Restore:Course',
        'ForceDelete:Course',
        'RestoreAny:Course',
        'ForceDeleteAny:Course',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }
});

describe('Course Authorization', function () {
    it('restricts actions to Developer and Kosma only, while allowing all to view', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['ViewAny:Course', 'Create:Course']);

        $kosma = User::factory()->create();
        $kosma->assignRole(RoleEnum::Kosma);
        $kosma->givePermissionTo(['ViewAny:Course', 'Create:Course']);

        $regularStudent = User::factory()->create();
        $regularStudent->assignRole(RoleEnum::Student);
        $regularStudent->givePermissionTo('ViewAny:Course');

        // All can view the list
        $this->actingAs($developer);
        Livewire::test(ManageCourses::class)->assertSuccessful();

        $this->actingAs($kosma);
        Livewire::test(ManageCourses::class)->assertSuccessful();

        $this->actingAs($regularStudent);
        Livewire::test(ManageCourses::class)->assertSuccessful();

        // Only Developer and Kosma can see the Create action
        $this->actingAs($developer);
        Livewire::test(ManageCourses::class)->assertActionVisible('createCourse');

        $this->actingAs($kosma);
        Livewire::test(ManageCourses::class)->assertActionVisible('createCourse');

        $this->actingAs($regularStudent);
        Livewire::test(ManageCourses::class)->assertActionHidden('createCourse');
    });
});

describe('Course Model', function () {
    it('supports Soft Deletes', function () {
        $course = Course::factory()->create();
        $course->delete();

        expect($course->trashed())->toBeTrue();
        expect(Course::find($course->id))->toBeNull();
    });

    it('has necessary relationships', function () {
        $course = Course::factory()->create();

        expect($course->assignments())->toBeInstanceOf(HasMany::class)
            ->and($course->materials())->toBeInstanceOf(HasMany::class)
            ->and($course->studyGroups())->toBeInstanceOf(BelongsToMany::class);
    });
});

describe('Course CRUD in Filament', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Course', 'Create:Course', 'Update:Course', 'Delete:Course']);
        $this->actingAs($user);
    });

    it('can list courses', function () {
        $courses = Course::factory()->count(3)->create();
        Livewire::test(ManageCourses::class)
            ->assertCanSeeTableRecords($courses);
    });

    it('can create a new course', function () {
        Livewire::test(ManageCourses::class)
            ->callAction('createCourse', [
                'code' => 'CS101',
                'name' => 'Intro to Programming',
                'credit' => 3,
                'semester' => 1,
                'lecturer' => 'Dr. Smith',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('courses', [
            'code' => 'CS101',
            'name' => 'Intro to Programming',
        ]);
    });

    it('can update course data', function () {
        $course = Course::factory()->create(['name' => 'Old Course Name']);

        Livewire::test(ManageCourses::class)
            ->callTableAction('editCourse', $course, [
                'name' => 'Updated Course Name',
            ])
            ->assertHasNoTableActionErrors();

        expect($course->refresh()->name)->toBe('Updated Course Name');
    });

    it('can delete a course', function () {
        $course = Course::factory()->create();

        Livewire::test(ManageCourses::class)
            ->callTableAction('delete', $course);

        expect(Course::find($course->id))->toBeNull();
        expect(Course::withTrashed()->find($course->id))->not->toBeNull();
    });
});

describe('Course Specific Features', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Course', 'Restore:Course', 'RestoreAny:Course']);
        $this->actingAs($user);
    });

    it('can restore a deleted course', function () {
        $course = Course::factory()->create();
        $course->delete();

        Livewire::test(ManageCourses::class)
            ->filterTable('trashed', 'only')
            ->callTableAction('restore', $course);

        expect($course->refresh()->trashed())->toBeFalse();
    });

    it('can perform bulk actions (Delete & Restore)', function () {
        $courses = Course::factory()->count(3)->create();

        // Bulk Delete
        Livewire::test(ManageCourses::class)
            ->callTableBulkAction('delete', $courses);

        foreach ($courses as $course) {
            expect($course->refresh()->trashed())->toBeTrue();
        }

        // Bulk Restore
        Livewire::test(ManageCourses::class)
            ->filterTable('trashed', 'only')
            ->callTableBulkAction('restore', $courses);

        foreach ($courses as $course) {
            expect($course->refresh()->trashed())->toBeFalse();
        }
    });
});

describe('Search, Sorting, and Filters', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Course']);
        $this->actingAs($user);
    });

    it('can search courses by name or code', function () {
        $target = Course::factory()->create([
            'name' => 'Target Course',
            'code' => 'TARGET123',
        ]);
        $other = Course::factory()->create([
            'name' => 'Other Course',
            'code' => 'OTHER456',
        ]);

        Livewire::test(ManageCourses::class)
            ->searchTable('Target Course')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords([$other])
            ->searchTable('TARGET123')
            ->assertCanSeeTableRecords([$target]);
    });

    it('can filter courses by trashed status', function () {
        Course::query()->forceDelete();
        $trashed = Course::factory()->create(['name' => 'Trashed Course', 'semester' => 1]);
        $trashed->delete();

        Livewire::test(ManageCourses::class)
            ->filterTable('trashed', 'only')
            ->assertSee('Trashed Course');
    });

    it('can sort courses by name', function () {
        Course::query()->forceDelete();

        $courseA = Course::factory()->create(['name' => 'A Course', 'semester' => 1]);
        $courseB = Course::factory()->create(['name' => 'B Course', 'semester' => 1]);
        $courseC = Course::factory()->create(['name' => 'C Course', 'semester' => 1]);

        Livewire::test(ManageCourses::class)
            ->sortTable('name', 'asc')
            ->assertCanSeeTableRecords([$courseA, $courseB, $courseC], inOrder: true);
    });
});
