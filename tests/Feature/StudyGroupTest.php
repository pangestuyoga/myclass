<?php

use App\Enums\RoleEnum;
use App\Filament\Pages\Learning\StudyGroup\Index as StudyGroupPage;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'View:StudyGroup',
        'Create:StudyGroup',
        'Update:StudyGroup',
        'Delete:StudyGroup',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }

    $this->currentSemester = app(GeneralSettings::class)->current_semester;
});

describe('Study Group Authorization', function () {
    it('allows all roles to view, but only Developer and Kosma can perform actions', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['View:StudyGroup', 'Create:StudyGroup']);

        $studentUser = User::factory()->create();
        $studentUser->assignRole(RoleEnum::Student);
        $studentUser->givePermissionTo(['View:StudyGroup']);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $this->actingAs($developer);
        Livewire::test(StudyGroupPage::class)
            ->assertSuccessful()
            ->assertActionVisible('createStudyGroup');

        $this->actingAs($studentUser);
        Livewire::test(StudyGroupPage::class)
            ->assertSuccessful()
            ->assertActionHidden('createStudyGroup');
    });
});

describe('Study Group Model', function () {
    it('has necessary relationships and supports soft deletes', function () {
        $course = Course::factory()->create();
        $leader = Student::factory()->create();
        $group = StudyGroup::factory()->create([
            'leader_id' => $leader->id,
        ]);
        $group->courses()->attach($course->id);

        expect($group->leader)->toBeInstanceOf(Student::class);
        expect($group->courses()->count())->toBe(1);
        expect($group->students())->toBeInstanceOf(BelongsToMany::class);

        $group->delete();
        expect($group->refresh()->trashed())->toBeTrue();
    });
});

describe('Study Group CRUD and Features', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['View:StudyGroup', 'Create:StudyGroup', 'Update:StudyGroup', 'Delete:StudyGroup']);
        $this->actingAs($user);
    });

    it('can create a study group', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $leader = Student::factory()->create();
        $member = Student::factory()->create();

        Livewire::test(StudyGroupPage::class)
            ->callAction('createStudyGroup', [
                'name' => 'Grup Heboh',
                'course_id' => [$course->id],
                'leader_id' => $leader->id,
                'students' => [$member->id],
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('study_groups', [
            'name' => 'Grup Heboh',
            'leader_id' => $leader->id,
        ]);

        $group = StudyGroup::where('name', 'Grup Heboh')->first();
        expect($group->courses->contains($course->id))->toBeTrue();
        expect($group->students->contains($member->id))->toBeTrue();
    });

    it('can update a study group', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $group = StudyGroup::factory()->create();
        $group->courses()->attach($course->id);

        $newMember = Student::factory()->create();

        Livewire::test(StudyGroupPage::class)
            ->callAction('editStudyGroup', [
                'name' => 'Grup Terupdate',
                'leader_id' => $group->leader_id,
                'students' => [$newMember->id],
                'course_id' => [$course->id],
            ], ['studyGroup' => $group->id])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('study_groups', [
            'id' => $group->id,
            'name' => 'Grup Terupdate',
        ]);

        expect($group->refresh()->students->contains($newMember->id))->toBeTrue();
    });

    it('can delete a study group', function () {
        $group = StudyGroup::factory()->create();

        Livewire::test(StudyGroupPage::class)
            ->callAction('deleteStudyGroup', [], ['studyGroup' => $group->id])
            ->assertHasNoActionErrors();

        $this->assertSoftDeleted('study_groups', [
            'id' => $group->id,
        ]);
    });
});

describe('Study Group Filters', function () {
    it('can filter results by course', function () {
        $course1 = Course::factory()->create(['semester' => $this->currentSemester]);
        $course2 = Course::factory()->create(['semester' => $this->currentSemester]);

        $group1 = StudyGroup::factory()->create(['name' => 'Group A']);
        $group1->courses()->attach($course1->id);

        $group2 = StudyGroup::factory()->create(['name' => 'Group B']);
        $group2->courses()->attach($course2->id);

        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['View:StudyGroup']);
        $this->actingAs($user);

        Livewire::test(StudyGroupPage::class)
            ->set('course_id', $course1->id)
            ->assertSet('course_id', $course1->id)
            ->assertSee('Group A')
            ->assertDontSee('Group B');
    });
});
