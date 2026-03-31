<?php

use App\Enums\IsActive;
use App\Enums\RoleEnum;
use App\Enums\Sex;
use App\Filament\Resources\Master\Students\Pages\CreateStudent;
use App\Filament\Resources\Master\Students\Pages\EditStudent;
use App\Filament\Resources\Master\Students\Pages\ListStudents;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        'ViewAny:Student',
        'Create:Student',
        'Update:Student',
        'Delete:Student',
        'Restore:Student',
        'ForceDelete:Student',
        'RestoreAny:Student',
        'ForceDeleteAny:Student',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }
});

describe('Student Authorization', function () {
    it('restricts access to Developer and Kosma roles only', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo('ViewAny:Student');

        $kosma = User::factory()->create();
        $kosma->assignRole(RoleEnum::Kosma);
        $kosma->givePermissionTo('ViewAny:Student');

        $regularStudent = User::factory()->create();
        $regularStudent->assignRole(RoleEnum::Student);

        $this->actingAs($developer);
        Livewire::test(ListStudents::class)->assertSuccessful();

        $this->actingAs($kosma);
        Livewire::test(ListStudents::class)->assertSuccessful();

        $this->actingAs($regularStudent);
        Livewire::test(ListStudents::class)->assertStatus(403);
    });
});

describe('Student Model', function () {
    it('has a relationship with User', function () {
        $student = Student::factory()->create();
        expect($student->user)->toBeInstanceOf(User::class);
    });

    it('supports Soft Deletes', function () {
        $student = Student::factory()->create();
        $student->delete();

        expect($student->trashed())->toBeTrue();
        expect(Student::find($student->id))->toBeNull();
    });

    it('has sex scopes', function () {
        Student::factory()->create(['sex' => Sex::Male]);
        Student::factory()->create(['sex' => Sex::Female]);

        expect(Student::male()->count())->toBe(1);
        expect(Student::female()->count())->toBe(1);
    });
});

describe('Student CRUD in Filament', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Student', 'Create:Student', 'Update:Student', 'Delete:Student']);
        $this->actingAs($user);
    });

    it('can list students', function () {
        $students = Student::factory()->count(3)->create();
        Livewire::test(ListStudents::class)
            ->assertCanSeeTableRecords($students);
    });

    it('can create a new student', function () {
        Livewire::test(CreateStudent::class)
            ->fillForm([
                'email' => 'new.student@example.com',
                'username' => 'newstudent',
                'full_name' => 'New Student',
                'student_number' => '9999999999',
                'sex' => Sex::Male->value,
                'phone_number' => '08123456789',
                'date_of_birth' => '2000-01-01',
                'place_of_birth' => 'Jakarta',
                'address' => 'Test address',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('students', [
            'full_name' => 'New Student',
            'student_number' => '9999999999',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'new.student@example.com',
            'username' => 'newstudent',
        ]);
    });

    it('can update student data', function () {
        $student = Student::factory()->create(['full_name' => 'Old Name']);

        Livewire::test(EditStudent::class, [
            'record' => $student->getRouteKey(),
        ])
            ->fillForm([
                'full_name' => 'Updated Name',
                'phone_number' => '0812 3333 4444',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($student->refresh()->full_name)->toBe('Updated Name');
    });

    it('can delete a student', function () {
        $student = Student::factory()->create();

        Livewire::test(ListStudents::class)
            ->callTableAction('delete', $student);

        expect(Student::find($student->id))->toBeNull();
        expect(Student::withTrashed()->find($student->id))->not->toBeNull();
    });
});

describe('Student Specific Features', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo([
            'ViewAny:Student',
            'Update:Student',
            'Delete:Student',
            'Restore:Student',
            'RestoreAny:Student',
            'ForceDelete:Student',
        ]);
        $this->actingAs($user);
    });

    it('can change student password', function () {
        $student = Student::factory()->create();
        $newPassword = 'NewPassword123!';

        Livewire::test(ListStudents::class)
            ->callTableAction('changePassword', $student, [
                'password' => $newPassword,
            ]);

        expect(Hash::check($newPassword, $student->user->refresh()->password))->toBeTrue();
    });

    it('can toggle student active status', function () {
        $student = Student::factory()->create();
        $user = $student->user;
        $user->update(['is_active' => IsActive::Active]);

        Livewire::test(ListStudents::class)
            ->call('updateTableColumnState', 'user.is_active', $student->getRouteKey(), false);

        expect($user->refresh()->is_active)->toBe(IsActive::Inactive);
    });

    it('can restore a deleted student', function () {
        $student = Student::factory()->create();
        $student->delete();

        Livewire::test(ListStudents::class)
            ->filterTable('trashed', 'only')
            ->callTableAction('restore', $student);

        expect($student->refresh()->trashed())->toBeFalse();
    });

    it('can perform bulk actions (Delete & Restore)', function () {
        $students = Student::factory()->count(3)->create();

        // Bulk Delete
        Livewire::test(ListStudents::class)
            ->callTableBulkAction('delete', $students);

        foreach ($students as $student) {
            expect($student->refresh()->trashed())->toBeTrue();
        }

        // Bulk Restore
        Livewire::test(ListStudents::class)
            ->filterTable('trashed', 'only')
            ->assertCanSeeTableRecords($students)
            ->callTableBulkAction('restore', $students);

        foreach ($students as $student) {
            expect($student->refresh()->trashed())->toBeFalse();
        }
    });

    it('can permanently delete a student (Force Delete)', function () {
        $student = Student::factory()->create();

        // Soft delete first for standard flow
        $student->delete();

        Livewire::test(ListStudents::class)
            ->filterTable('trashed', 'only')
            ->callTableAction('forceDelete', $student);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    });
});

describe('Validations and Side Effects', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Student', 'Create:Student', 'Update:Student']);
        $this->actingAs($user);
    });

    it('clears active sessions when user status is toggled to Inactive', function () {
        $student = Student::factory()->create();
        $user = $student->user;
        $user->update(['is_active' => IsActive::Active]);

        // Mock an active session in the database
        DB::table('sessions')->insert([
            'id' => 'test_session_id',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Pest Test',
            'payload' => 'test_payload',
            'last_activity' => time(),
        ]);

        $this->assertDatabaseHas('sessions', ['user_id' => $user->id]);

        Livewire::test(ListStudents::class)
            ->call('updateTableColumnState', 'user.is_active', $student->getRouteKey(), false);

        expect($user->refresh()->is_active)->toBe(IsActive::Inactive);
        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
    });

    it('enforces unique student number validation', function () {
        Student::factory()->create(['student_number' => '1234567890']);

        Livewire::test(CreateStudent::class)
            ->fillForm(['student_number' => '1234567890'])
            ->call('create')
            ->assertHasFormErrors(['student_number' => 'unique']);
    });

    it('enforces student minimum age requirement (15 years)', function () {
        // Form allows maxDate is Carbon::now()->subYears(15)
        $youngDate = now()->subYears(10)->format('Y-m-d');

        Livewire::test(CreateStudent::class)
            ->fillForm(['date_of_birth' => $youngDate])
            ->call('create')
            ->assertHasFormErrors(['date_of_birth']);
    });
});

describe('Search, Sorting, and Filters', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['ViewAny:Student']);
        $this->actingAs($user);
    });

    it('can search students by name or NIM', function () {
        $target = Student::factory()->create([
            'full_name' => 'Target Search',
            'student_number' => '1122334455',
        ]);
        $other = Student::factory()->create([
            'full_name' => 'Other Student',
            'student_number' => '9988776655',
        ]);

        Livewire::test(ListStudents::class)
            ->searchTable('Target Search')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords([$other])
            ->searchTable('1122334455')
            ->assertCanSeeTableRecords([$target]);
    });

    it('can filter students by sex', function () {
        $male = Student::factory()->create(['sex' => Sex::Male]);
        $female = Student::factory()->create(['sex' => Sex::Female]);

        Livewire::test(ListStudents::class)
            ->filterTable('sex', Sex::Male->value)
            ->assertCanSeeTableRecords([$male])
            ->assertCanNotSeeTableRecords([$female]);
    });

    it('can sort students by name', function () {
        Student::query()->forceDelete();

        $studentA = Student::factory()->create(['full_name' => 'A Student']);
        $studentB = Student::factory()->create(['full_name' => 'B Student']);
        $studentC = Student::factory()->create(['full_name' => 'C Student']);

        Livewire::test(ListStudents::class)
            ->sortTable('full_name', 'asc')
            ->assertCanSeeTableRecords([$studentA, $studentB, $studentC], inOrder: true);
    });
});
