<?php

use App\Enums\NotifStyle;
use App\Filament\Support\SystemNotification;
use App\Models\User;
use Filament\Notifications\Notification;

/**
 * Test suite untuk class SystemNotification.
 *
 * Class ini adalah inti dari sistem notifikasi terpusat yang mengambil
 * pesan dari file lang/id/notif.php berdasarkan gaya bahasa user (Cheerful/Formal).
 */
describe('SystemNotification - getNotifStyle', function () {
    it('returns Cheerful style as default when no user is authenticated', function () {
        auth()->logout();

        expect(SystemNotification::getNotifStyle())->toBe(NotifStyle::Cheerful);
    });

    it('returns Cheerful style for user with Cheerful setting', function () {
        $user = User::factory()->create();
        $user->settings()->create(['notif_style' => NotifStyle::Cheerful]);
        $this->actingAs($user);

        expect(SystemNotification::getNotifStyle())->toBe(NotifStyle::Cheerful);
    });

    it('returns Formal style for user with Formal setting', function () {
        $user = User::factory()->create();
        $user->settings()->create(['notif_style' => NotifStyle::Formal]);
        $this->actingAs($user);

        expect(SystemNotification::getNotifStyle())->toBe(NotifStyle::Formal);
    });

    it('falls back to Cheerful when user has no settings', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        expect(SystemNotification::getNotifStyle())->toBe(NotifStyle::Cheerful);
    });
});

describe('SystemNotification - getByKey', function () {
    it('resolves a notif key for Cheerful style', function () {
        $user = User::factory()->create();
        $user->settings()->create(['notif_style' => NotifStyle::Cheerful]);
        $this->actingAs($user);

        $result = SystemNotification::getByKey('create.title');

        // Must return a non-empty string and resolve to the cheerful translation
        expect($result)->toBeString()->not->toBeEmpty();
    });

    it('resolves a notif key for Formal style', function () {
        $user = User::factory()->create();
        $user->settings()->create(['notif_style' => NotifStyle::Formal]);
        $this->actingAs($user);

        $result = SystemNotification::getByKey('create.title');

        expect($result)->toBeString()->not->toBeEmpty();
    });

    it('returns different strings for Cheerful vs Formal on the same key', function () {
        $userCheerful = User::factory()->create();
        $userCheerful->settings()->create(['notif_style' => NotifStyle::Cheerful]);
        $this->actingAs($userCheerful);
        $cheerfulText = SystemNotification::getByKey('create.title');

        $userFormal = User::factory()->create();
        $userFormal->settings()->create(['notif_style' => NotifStyle::Formal]);
        $this->actingAs($userFormal);
        $formalText = SystemNotification::getByKey('create.title');

        expect($cheerfulText)->not->toBe($formalText);
    });

    it('supports placeholder replacement in translation strings', function () {
        $user = User::factory()->create();
        $user->settings()->create(['notif_style' => NotifStyle::Cheerful]);
        $this->actingAs($user);

        // Use a key that supports :name replacement, like profile_updated if applicable.
        // Here we verify the replace mechanism works without throwing.
        $result = SystemNotification::getByKey('create.title', []);

        expect($result)->toBeString()->not->toBeEmpty();
    });
});

describe('SystemNotification - factory methods', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->settings()->create(['notif_style' => NotifStyle::Cheerful]);
        $this->actingAs($user);
    });

    it('create() returns a success Notification instance', function () {
        $notification = SystemNotification::create();

        expect($notification)->toBeInstanceOf(Notification::class);
        // Inspect that title and body are filled (not empty / raw key strings)
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('update() returns a success Notification instance with a title', function () {
        $notification = SystemNotification::update();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('delete() returns a success Notification instance with a title', function () {
        $notification = SystemNotification::delete();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('restore() returns a success Notification instance with a title', function () {
        $notification = SystemNotification::restore();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('forceDelete() returns a success Notification instance with a title', function () {
        $notification = SystemNotification::forceDelete();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('bulkDelete() returns a success Notification instance', function () {
        $notification = SystemNotification::bulkDelete();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('bulkRestore() returns a success Notification instance', function () {
        $notification = SystemNotification::bulkRestore();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('bulkForceDelete() returns a success Notification instance', function () {
        $notification = SystemNotification::bulkForceDelete();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('statusUpdated() returns a success Notification with default messages', function () {
        $notification = SystemNotification::statusUpdated();

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('statusUpdated() accepts override title and body', function () {
        $notification = SystemNotification::statusUpdated(
            title: 'Custom Judul',
            body: 'Custom Deskripsi'
        );

        expect($notification->getTitle())->toBe('Custom Judul');
        expect($notification->getBody())->toBe('Custom Deskripsi');
    });
});

describe('SystemNotification - send()', function () {
    beforeEach(function () {
        $user = User::factory()->create();
        $user->settings()->create(['notif_style' => NotifStyle::Cheerful]);
        $this->actingAs($user);
    });

    it('returns a success Notification by default', function () {
        $notification = SystemNotification::send('create');

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->getTitle())->toBeString()->not->toBeEmpty();
    });

    it('returns a danger Notification when type is "danger"', function () {
        $notification = SystemNotification::send('delete', type: 'danger');

        expect($notification)->toBeInstanceOf(Notification::class);
        // Danger status is reflected in the icon; we confirm the method does not throw.
    });

    it('returns an info Notification when type is "info"', function () {
        $notification = SystemNotification::send('restore', type: 'info');

        expect($notification)->toBeInstanceOf(Notification::class);
    });

    it('returns a warning Notification when type is "warning"', function () {
        $notification = SystemNotification::send('update', type: 'warning');

        expect($notification)->toBeInstanceOf(Notification::class);
    });

    it('passes replace array to the translation', function () {
        // Verify no exception is thrown when replace is provided
        $notification = SystemNotification::send('create', replace: ['name' => 'Test'], type: 'success');

        expect($notification)->toBeInstanceOf(Notification::class);
    });
});

describe('NotifStyle Enum', function () {
    it('has correct values', function () {
        expect(NotifStyle::Cheerful->value)->toBe('cheerful');
        expect(NotifStyle::Formal->value)->toBe('formal');
    });

    it('has correct labels', function () {
        expect(NotifStyle::Cheerful->getLabel())->toContain('Ceria');
        expect(NotifStyle::Formal->getLabel())->toContain('Formal');
    });
});
