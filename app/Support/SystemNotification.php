<?php

namespace App\Filament\Support;

use Filament\Notifications\Notification;

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
        return self::make()
            ->title('Data Berhasil Disimpan ✨')
            ->body('Data baru telah berhasil ditambahkan ke dalam sistem.')
            ->success();
    }

    /**
     * Notification for successful record update.
     */
    public static function update(): Notification
    {
        return self::make()
            ->title('Perubahan Berhasil Disimpan ✅')
            ->body('Pembaruan data telah berhasil disimpan dengan aman.')
            ->success();
    }

    /**
     * Notification for successful record deletion (soft delete).
     */
    public static function delete(): Notification
    {
        return self::make()
            ->title('Data Berhasil Dihapus 🗑️')
            ->body('Data yang dipilih telah berhasil dihapus dari sistem.')
            ->success();
    }

    /**
     * Notification for successful permanent deletion.
     */
    public static function forceDelete(): Notification
    {
        return self::make()
            ->title('Data Dihapus Secara Permanen 🛡️')
            ->body('Data telah dihapus secara permanen dan tidak dapat dipulihkan kembali.')
            ->success();
    }

    /**
     * Notification for successful record restoration.
     */
    public static function restore(): Notification
    {
        return self::make()
            ->title('Data Berhasil Dipulihkan 🔄')
            ->body('Data telah berhasil dikembalikan dan diaktifkan kembali di sistem.')
            ->success();
    }

    /**
     * Notification for successful status change/toggle.
     */
    public static function statusUpdated(?string $title = null, ?string $body = null): Notification
    {
        return self::make()
            ->title($title ?? 'Status Berhasil Diperbarui ⚡')
            ->body($body ?? 'Status data telah berhasil diperbarui dan sudah aktif kembali.')
            ->success();
    }

    /**
     * Notification for successful bulk deletion.
     */
    public static function bulkDelete(): Notification
    {
        return self::make()
            ->title('Data Massal Berhasil Dihapus 📁')
            ->body('Seluruh data yang dipilih telah berhasil dihapus dari sistem.')
            ->success();
    }

    /**
     * Notification for successful bulk permanent deletion.
     */
    public static function bulkForceDelete(): Notification
    {
        return self::make()
            ->title('Penghapusan Massal Permanen Berhasil ⚙️')
            ->body('Seluruh data terpilih telah dihapus secara permanen dari sistem.')
            ->success();
    }

    /**
     * Notification for successful bulk restoration.
     */
    public static function bulkRestore(): Notification
    {
        return self::make()
            ->title('Pemulihan Massal Berhasil ✨')
            ->body('Seluruh data yang dipilih telah berhasil dipulihkan kembali ke sistem.')
            ->success();
    }

    /**
     * Custom cheerful success notification.
     */
    public static function success(string $title, string $body): Notification
    {
        return self::make()
            ->title($title)
            ->body($body)
            ->success();
    }

    /**
     * Custom cheerful info notification.
     */
    public static function info(string $title, string $body): Notification
    {
        return self::make()
            ->title($title)
            ->body($body)
            ->info();
    }

    /**
     * Custom cheerful warning notification.
     */
    public static function warning(string $title, string $body): Notification
    {
        return self::make()
            ->title($title)
            ->body($body)
            ->warning();
    }

    /**
     * Custom cheerful danger notification.
     */
    public static function danger(string $title, string $body): Notification
    {
        return self::make()
            ->title($title)
            ->body($body)
            ->danger();
    }
}
