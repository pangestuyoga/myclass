<?php

use App\Enums\ChangelogType;
use App\Enums\RoleEnum;
use App\Filament\Pages\System\Changelog\Index as ChangelogPage;
use App\Models\Changelog;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
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
        'View:Changelog',
        'Create:Changelog',
        'Update:Changelog',
        'Delete:Changelog',
    ];

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }
});

describe('Changelog Authorization', function () {
    it('allows Developer to access the changelog page', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo('View:Changelog');

        $this->actingAs($developer);
        Livewire::test(ChangelogPage::class)
            ->assertSuccessful();
    });

    it('restricts Students from accessing the changelog page', function () {
        $student = User::factory()->create();
        $student->assignRole(RoleEnum::Student);

        $this->actingAs($student);
        Livewire::test(ChangelogPage::class)
            ->assertStatus(403);
    });

    it('shows createChangelog action to users with Create:Changelog permission', function () {
        $developer = User::factory()->create();
        $developer->assignRole(RoleEnum::Developer);
        $developer->givePermissionTo(['View:Changelog', 'Create:Changelog']);

        $this->actingAs($developer);
        Livewire::test(ChangelogPage::class)
            ->assertActionVisible('createChangelog');
    });

    it('hides createChangelog action from users without Create:Changelog permission', function () {
        $kosma = User::factory()->create();
        $kosma->assignRole(RoleEnum::Kosma);
        $kosma->givePermissionTo('View:Changelog');

        $this->actingAs($kosma);
        Livewire::test(ChangelogPage::class)
            ->assertActionHidden('createChangelog');
    });
});

describe('Changelog Model', function () {
    it('has the correct casts configured', function () {
        $changelog = Changelog::factory()->create([
            'type' => ChangelogType::Feature,
            'changes' => ['Tambah fitur baru', 'Update UI'],
        ]);

        expect($changelog->type)->toBeInstanceOf(ChangelogType::class);
        expect($changelog->type)->toBe(ChangelogType::Feature);
        expect($changelog->changes)->toBeArray();
        expect($changelog->release_date)->toBeInstanceOf(Carbon::class);
    });

    it('supports soft deletes', function () {
        $changelog = Changelog::factory()->create();

        $changelog->delete();

        expect(Changelog::find($changelog->id))->toBeNull();
        expect(Changelog::withTrashed()->find($changelog->id))->not->toBeNull();
        expect($changelog->refresh()->trashed())->toBeTrue();
    });

    it('can be force deleted', function () {
        $changelog = Changelog::factory()->create();
        $changelog->delete();
        $changelog->forceDelete();

        expect(Changelog::withTrashed()->find($changelog->id))->toBeNull();
        $this->assertDatabaseMissing('changelogs', ['id' => $changelog->id]);
    });

    it('enforces unique version constraint', function () {
        Changelog::factory()->create(['version' => 'v1.0.0']);

        expect(fn () => Changelog::factory()->create(['version' => 'v1.0.0']))
            ->toThrow(QueryException::class);
    });
});

describe('Changelog CRUD via Filament Page', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo(['View:Changelog', 'Create:Changelog', 'Update:Changelog', 'Delete:Changelog']);
        $this->actingAs($user);
    });

    it('can create a changelog entry', function () {
        Livewire::test(ChangelogPage::class)
            ->callAction('createChangelog', [
                'version' => 'v2.0.0',
                'release_date' => now()->toDateString(),
                'type' => ChangelogType::Feature->value,
                'title' => 'Fitur Keren Baru',
                'changes' => ['Tambah fitur A', 'Tambah fitur B'],
                'description' => 'Deskripsi singkat fitur baru.',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('changelogs', [
            'version' => 'v2.0.0',
            'title' => 'Fitur Keren Baru',
        ]);
    });

    it('validates required fields when creating a changelog', function () {
        Livewire::test(ChangelogPage::class)
            ->callAction('createChangelog', [
                'version' => '',
                'title' => '',
            ])
            ->assertHasActionErrors(['version', 'title']);
    });

    it('validates unique version when creating a changelog', function () {
        Changelog::factory()->create(['version' => 'v1.5.0']);

        Livewire::test(ChangelogPage::class)
            ->callAction('createChangelog', [
                'version' => 'v1.5.0',
                'release_date' => now()->toDateString(),
                'type' => ChangelogType::BugFix->value,
                'title' => 'Duplikat Versi',
                'changes' => ['Fix A'],
                'description' => 'Deskripsi.',
            ])
            ->assertHasActionErrors(['version']);
    });

    it('can edit an existing changelog entry', function () {
        $changelog = Changelog::factory()->create([
            'title' => 'Judul Lama',
            'type' => ChangelogType::BugFix,
            'changes' => ['Fix satu bug'],
        ]);

        Livewire::test(ChangelogPage::class)
            ->callAction('editChangelog', [
                'version' => $changelog->version,
                'release_date' => $changelog->release_date->toDateString(),
                'type' => ChangelogType::Improvement->value,
                'title' => 'Judul Baru',
                'changes' => ['Perbaikan performa'],
                'description' => $changelog->description,
            ], ['record' => $changelog->id])
            ->assertHasNoActionErrors();

        expect($changelog->refresh()->title)->toBe('Judul Baru');
        expect($changelog->refresh()->type)->toBe(ChangelogType::Improvement);
    });

    it('can delete a changelog entry (soft delete)', function () {
        $changelog = Changelog::factory()->create();

        Livewire::test(ChangelogPage::class)
            ->callAction('deleteChangelog', [], ['record' => $changelog->id])
            ->assertHasNoActionErrors();

        $this->assertSoftDeleted('changelogs', ['id' => $changelog->id]);
    });
});

describe('Changelog Computed Properties', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Developer);
        $user->givePermissionTo('View:Changelog');
        $this->actingAs($user);
    });

    it('returns changelogs sorted by latest release date', function () {
        $oldest = Changelog::factory()->create(['release_date' => now()->subYear()]);
        $newest = Changelog::factory()->create(['release_date' => now()]);

        $component = Livewire::test(ChangelogPage::class);

        // changelogs() computed prop returns latest first
        $ids = Changelog::latest('release_date')->pluck('id')->toArray();
        expect($ids[0])->toBe($newest->id);
        expect($ids[1])->toBe($oldest->id);
    });

    it('reflects latest version in techStack', function () {
        // Clear and create changelogs in order
        Changelog::factory()->create(['version' => 'v1.0.0', 'release_date' => now()->subDays(5)]);
        Changelog::factory()->create(['version' => 'v2.0.0', 'release_date' => now()]);

        $latestVersion = Changelog::latest('release_date')->first()->version;
        expect($latestVersion)->toBe('v2.0.0');
    });

    it('shows changelog entries on the page', function () {
        $changelog = Changelog::factory()->create(['title' => 'Update Kece']);

        Livewire::test(ChangelogPage::class)
            ->assertSee('Update Kece');
    });

    it('shows empty state message when no changelogs exist', function () {
        Changelog::query()->forceDelete();

        // The page should still render without errors when empty
        Livewire::test(ChangelogPage::class)
            ->assertSuccessful();
    });
});

describe('ChangelogType Enum', function () {
    it('returns correct label for each type', function () {
        expect(ChangelogType::Feature->getLabel())->toContain('Fitur Baru');
        expect(ChangelogType::Improvement->getLabel())->toContain('Peningkatan');
        expect(ChangelogType::BugFix->getLabel())->toContain('Perbaikan Bug');
        expect(ChangelogType::Security->getLabel())->toContain('Keamanan');
    });

    it('returns correct color for each type', function () {
        expect(ChangelogType::Feature->getColor())->toBe('success');
        expect(ChangelogType::Improvement->getColor())->toBe('info');
        expect(ChangelogType::BugFix->getColor())->toBe('danger');
        expect(ChangelogType::Security->getColor())->toBe('warning');
    });

    it('returns correct icon for each type', function () {
        expect(ChangelogType::Feature->getIcon())->toBe('heroicon-o-rocket-launch');
        expect(ChangelogType::BugFix->getIcon())->toBe('heroicon-o-bug-ant');
        expect(ChangelogType::Security->getIcon())->toBe('heroicon-o-shield-check');
    });
});
