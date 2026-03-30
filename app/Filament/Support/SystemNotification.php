<?php

namespace App\Filament\Support;

use App\Enums\NotifStyle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SystemNotification
{
    /**
     * Create a new notification instance.
     */
    public static function make(): Notification
    {
        return Notification::make();
    }

    /**
     * Notification for successful record creation.
     */
    public static function create(): Notification
    {
        return self::success(
            self::getMessage('Hore! Data Tersimpan 🎉✨', 'Data Berhasil Disimpan'),
            self::getMessage('Data baru berhasil ditambahkan! Sistem sudah menyimpannya dengan aman. 🚀💪', 'Data baru telah berhasil ditambahkan ke dalam sistem.')
        );
    }

    /**
     * Notification for successful record update.
     */
    public static function update(): Notification
    {
        return self::success(
            self::getMessage('Mantap! Data Diperbarui ✅🔥', 'Perubahan Tersimpan'),
            self::getMessage('Perubahan berhasil disimpan! Data sekarang sudah up-to-date dan segar lagi. ✨👌', 'Perubahan pada data telah berhasil diperbarui dan disimpan.')
        );
    }

    /**
     * Notification for successful record deletion (soft delete).
     */
    public static function delete(): Notification
    {
        return self::success(
            self::getMessage('Oke, Data Dihapus 🗑️👋', 'Data Dihapus'),
            self::getMessage('Data tersebut sudah berhasil dihapus dari sistem. Semuanya bersih dan rapi sekarang! 😊🚮', 'Data tersebut telah berhasil dihapus dari sistem aplikasi.')
        );
    }

    /**
     * Notification for successful permanent deletion.
     */
    public static function forceDelete(): Notification
    {
        return self::success(
            self::getMessage('Selamat Tinggal Selamanya 👋😢', 'Data Dihapus Permanen'),
            self::getMessage('Data telah dihapus permanen dan tidak bisa kembali. Semoga ini keputusan yang tepat! 🚮💨', 'Data tersebut telah dihapus secara permanen dari sistem.')
        );
    }

    /**
     * Notification for successful record restoration.
     */
    public static function restore(): Notification
    {
        return self::success(
            self::getMessage('Welcome Back! Data Pulih ♻️✨', 'Data Berhasil Dipulihkan'),
            self::getMessage('Data berhasil dikembalikan! Hati-hati ya, jangan sampai terhapus lagi. 😉👍', 'Data yang dihapus sebelumnya telah berhasil dipulihkan ke dalam sistem.')
        );
    }

    /**
     * Notification for successful status change/toggle.
     */
    public static function statusUpdated(?string $title = null, ?string $body = null): Notification
    {
        return self::success(
            $title ?? self::getMessage('Status Berubah! 🔄✨', 'Status Diperbarui'),
            $body ?? self::getMessage('Status data berhasil diperbarui. Perubahan langsung aktif ya! 👍', 'Status data telah berhasil diperbarui dan telah diterapkan.')
        );
    }

    /**
     * Notification for successful bulk deletion.
     */
    public static function bulkDelete(): Notification
    {
        return self::success(
            self::getMessage('Oke, Banyak Data Dihapus 🗑️👋', 'Data Berhasil Dihapus'),
            self::getMessage('Semua data yang dipilih berhasil dihapus. Sistem makin lega deh! 😊', 'Seluruh data yang dipilih telah berhasil dihapus dari sistem.')
        );
    }

    /**
     * Notification for successful bulk permanent deletion.
     */
    public static function bulkForceDelete(): Notification
    {
        return self::success(
            self::getMessage('Bye Bye Semua! 👋🔥', 'Data Dihapus Permanen'),
            self::getMessage('Data yang dipilih sudah dihapus permanen. Bersih total! 🧹💨', 'Data yang dipilih telah berhasil dihapus secara permanen.')
        );
    }

    /**
     * Notification for successful bulk restoration.
     */
    public static function bulkRestore(): Notification
    {
        return self::success(
            self::getMessage('Hore! Banyak Data Pulih ♻️🎉', 'Data Berhasil Dipulihkan'),
            self::getMessage('Data-data tersebut sudah kembali aktif. Selamat bekerja kembali! 💪✨', 'Seluruh data yang dipilih telah berhasil dikembalikan ke posisi semula.')
        );
    }

    /**
     * Success notification wrapper.
     */
    public static function success(string $title, string $body, ?string $formalTitle = null, ?string $formalBody = null): Notification
    {
        $finalTitle = self::getMessage($title, $formalTitle ?? $title);
        $finalBody = self::getMessage($body, $formalBody ?? $body);

        return self::applyStyle(
            self::make()
                ->title(self::clean($finalTitle))
                ->body(self::clean($finalBody))
                ->success(),
            'success'
        );
    }

    /**
     * Custom info notification.
     */
    public static function info(string $title, string $body, ?string $formalTitle = null, ?string $formalBody = null): Notification
    {
        $finalTitle = self::getMessage($title, $formalTitle ?? $title);
        $finalBody = self::getMessage($body, $formalBody ?? $body);

        return self::applyStyle(
            self::make()
                ->title(self::clean($finalTitle))
                ->body(self::clean($finalBody))
                ->info(),
            'info'
        );
    }

    /**
     * Custom warning notification.
     */
    public static function warning(string $title, string $body, ?string $formalTitle = null, ?string $formalBody = null): Notification
    {
        $finalTitle = self::getMessage($title, $formalTitle ?? $title);
        $finalBody = self::getMessage($body, $formalBody ?? $body);

        return self::applyStyle(
            self::make()
                ->title(self::clean($finalTitle))
                ->body(self::clean($finalBody))
                ->warning(),
            'warning'
        );
    }

    /**
     * Custom danger notification.
     */
    public static function danger(string $title, string $body, ?string $formalTitle = null, ?string $formalBody = null): Notification
    {
        $finalTitle = self::getMessage($title, $formalTitle ?? $title);
        $finalBody = self::getMessage($body, $formalBody ?? $body);

        return self::applyStyle(
            self::make()
                ->title(self::clean($finalTitle))
                ->body(self::clean($finalBody))
                ->danger(),
            'danger'
        );
    }

    /**
     * Apply the user's selected style to the notification.
     */
    protected static function applyStyle(Notification $notification, string $status): Notification
    {
        $isCheerful = self::getNotifStyle() === NotifStyle::Cheerful;

        if ($isCheerful) {
            $notification
                ->duration(6000)
                ->icon(match ($status) {
                    'success' => 'heroicon-o-sparkles',
                    'danger' => 'heroicon-o-fire',
                    'warning' => 'heroicon-o-bolt',
                    'info' => 'heroicon-o-megaphone',
                    default => null,
                });
        } else {
            $notification
                ->icon(null)
                ->duration(4000);
        }

        return $notification;
    }

    /**
     * Get the current user's notification style preference.
     */
    public static function getNotifStyle(): NotifStyle
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            return $user?->settings?->notif_style ?? NotifStyle::Cheerful;
        } catch (\Throwable $e) {
            return NotifStyle::Cheerful;
        }
    }

    /**
     * Get message based on the user's notification style preference.
     */
    public static function getMessage(string $cheerful, string $formal): string
    {
        return self::getNotifStyle() === NotifStyle::Cheerful ? $cheerful : $formal;
    }

    /**
     * Strip emojis if the notification style is formal.
     */
    public static function clean(string $text): string
    {
        if (self::getNotifStyle() === NotifStyle::Formal) {
            // Comprehensive regex to remove emojis and symbols
            $regex = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1F1E0}-\x{1F1FF}]/u';

            return trim(preg_replace($regex, '', $text));
        }

        return $text;
    }
}
