<?php

use App\Enums\RoleEnum;
use App\Filament\Resources\Learning\Materials\Pages\ManageMaterials;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use App\Settings\GeneralSettings;
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
        'ViewAny:Material',
        'Create:Material',
        'View:Material',
        'Update:Material',
        'Delete:Material',
        'Restore:Material',
        'ForceDelete:Material',
        'RestoreAny:Material',
        'ForceDeleteAny:Material',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }

    Storage::fake('public');
});

describe('Material Authorization', function () {
    it('allows all roles to view the list but restricts actions to Developer and Kosma', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['ViewAny:Material', 'Create:Material']);

        $kosma = User::factory()->create();
        $kosma->assignRole(RoleEnum::Kosma);
        $kosma->givePermissionTo(['ViewAny:Material', 'Create:Material']);

        $regularStudent = User::factory()->create();
        $regularStudent->assignRole(RoleEnum::Student);
        $regularStudent->givePermissionTo('ViewAny:Material');

        $this->actingAs($developer);
        Livewire::test(ManageMaterials::class)->assertSuccessful()
            ->assertActionVisible('createMaterial');

        $this->actingAs($kosma);
        Livewire::test(ManageMaterials::class)->assertSuccessful()
            ->assertActionVisible('createMaterial');

        $this->actingAs($regularStudent);
        Livewire::test(ManageMaterials::class)->assertSuccessful()
            ->assertActionHidden('createMaterial');
    });
});

describe('Material Model', function () {
    it('has relationships with Course and ClassSession', function () {
        $material = Material::factory()->create();
        expect($material->course)->toBeInstanceOf(Course::class);
        expect($material->classSession)->toBeInstanceOf(ClassSession::class);
    });

    it('supports Soft Deletes', function () {
        $material = Material::factory()->create();
        $material->delete();

        expect($material->trashed())->toBeTrue();
        expect(Material::find($material->id))->toBeNull();
    });
});

describe('Material CRUD', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Material', 'Create:Material', 'Update:Material', 'Delete:Material']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can create a material', function () {
        $course = Course::factory()->create(['semester' => $this->currentSemester]);
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        Livewire::test(ManageMaterials::class)
            ->callAction('createMaterial', [
                'course_id' => $course->id,
                'title' => 'Introduction to PHP',
                'description' => 'Basics of PHP programming.',
                'pdf' => [$file],
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('materials', [
            'course_id' => $course->id,
            'title' => 'Introduction to PHP',
        ]);
    });

    it('can update material data', function () {
        $material = Material::factory()->create(['class_session_id' => null]);
        $material->course->update(['semester' => $this->currentSemester]);
        $file = UploadedFile::fake()->create('updated.pdf', 100, 'application/pdf');

        Livewire::test(ManageMaterials::class)
            ->callTableAction('editMaterial', $material, [
                'title' => 'Updated Title',
                'course_id' => $material->course_id,
                'pdf' => [$file],
            ])
            ->assertHasNoTableActionErrors();

        expect($material->refresh()->title)->toBe('Updated Title');
    });

    it('can delete a material', function () {
        $material = Material::factory()->create();
        $material->course->update(['semester' => $this->currentSemester]);

        Livewire::test(ManageMaterials::class)
            ->callTableAction('delete', $material);

        expect(Material::find($material->id))->toBeNull();
    });
});

describe('Material Restoration and Bulk Actions', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Material', 'Restore:Material', 'RestoreAny:Material']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can restore a deleted material', function () {
        $material = Material::factory()->create();
        $material->course->update(['semester' => $this->currentSemester]);
        $material->delete();

        Livewire::test(ManageMaterials::class)
            ->filterTable('trashed', 'only')
            ->callTableAction('restore', $material);

        expect($material->refresh()->trashed())->toBeFalse();
    });

    it('can perform bulk actions (Delete & Restore)', function () {
        $materials = Material::factory()->count(3)->create();
        foreach ($materials as $m) {
            $m->course->update(['semester' => $this->currentSemester]);
        }

        // Bulk Delete
        Livewire::test(ManageMaterials::class)
            ->callTableBulkAction('delete', $materials);

        foreach ($materials as $material) {
            expect($material->refresh()->trashed())->toBeTrue();
        }

        // Bulk Restore
        Livewire::test(ManageMaterials::class)
            ->filterTable('trashed', 'only')
            ->callTableBulkAction('restore', $materials);

        foreach ($materials as $material) {
            expect($material->refresh()->trashed())->toBeFalse();
        }
    });
});

describe('Search, Sorting, and Filters', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Material']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can search materials by title', function () {
        $target = Material::factory()->create(['title' => 'Specific Material Title']);
        $target->course->update(['semester' => $this->currentSemester]);

        $other = Material::factory()->create(['title' => 'Generic Title']);
        $other->course->update(['semester' => $this->currentSemester]);

        Livewire::test(ManageMaterials::class)
            ->searchTable('Specific Material Title')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords([$other]);
    });

    it('can filter materials by course', function () {
        $course1 = Course::factory()->create(['semester' => $this->currentSemester]);
        $course2 = Course::factory()->create(['semester' => $this->currentSemester]);

        $m1 = Material::factory()->create(['course_id' => $course1->id]);
        $m1->course->update(['semester' => $this->currentSemester]);

        $m2 = Material::factory()->create(['course_id' => $course2->id]);
        $m2->course->update(['semester' => $this->currentSemester]);

        Livewire::test(ManageMaterials::class)
            ->filterTable('course_id', $course1->id)
            ->assertCanSeeTableRecords([$m1])
            ->assertCanNotSeeTableRecords([$m2]);
    });
});

describe('Material View Actions', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Material', 'View:Material']);
        $this->actingAs($user);
        $this->currentSemester = app(GeneralSettings::class)->current_semester;
    });

    it('can see viewMaterial action when media exists', function () {
        $material = Material::factory()->create();
        $material->course->update(['semester' => $this->currentSemester]);

        $material->addMedia(UploadedFile::fake()->create('lecture.pdf', 100))
            ->toMediaCollection('materials');

        expect($material->hasMedia('materials'))->toBeTrue();

        Livewire::test(ManageMaterials::class)
            ->assertTableActionVisible('viewMaterial', $material);
    });
});
