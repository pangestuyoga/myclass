<?php

namespace App\Filament\Support;

use App\Enums\NotifStyle;
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
     * Get the current user's Notification style preference.
     */
    public static function getNotifStyle(): NotifStyle
    {
        try {
            return auth()->user()?->settings?->notif_style ?? NotifStyle::Cheerful;
        } catch (\Throwable $e) {
            return NotifStyle::Cheerful;
        }
    }

    public static function create(): Notification
    {
        return self::make()
            ->title(self::getByKey('create.title'))
            ->body(self::getByKey('create.body'))
            ->success();
    }

    public static function update(): Notification
    {
        return self::make()
            ->title(self::getByKey('update.title'))
            ->body(self::getByKey('update.body'))
            ->success();
    }

    public static function delete(): Notification
    {
        return self::make()
            ->title(self::getByKey('delete.title'))
            ->body(self::getByKey('delete.body'))
            ->success();
    }

    public static function forceDelete(): Notification
    {
        return self::make()
            ->title(self::getByKey('force_delete.title'))
            ->body(self::getByKey('force_delete.body'))
            ->success();
    }

    public static function restore(): Notification
    {
        return self::make()
            ->title(self::getByKey('restore.title'))
            ->body(self::getByKey('restore.body'))
            ->success();
    }

    public static function statusUpdated(?string $title = null, ?string $body = null): Notification
    {
        return self::make()
            ->title($title ?? self::getByKey('status_updated.title'))
            ->body($body ?? self::getByKey('status_updated.body'))
            ->success();
    }

    public static function bulkDelete(): Notification
    {
        return self::make()
            ->title(self::getByKey('bulk_delete.title'))
            ->body(self::getByKey('bulk_delete.body'))
            ->success();
    }

    public static function bulkForceDelete(): Notification
    {
        return self::make()
            ->title(self::getByKey('bulk_force_delete.title'))
            ->body(self::getByKey('bulk_force_delete.body'))
            ->success();
    }

    public static function bulkRestore(): Notification
    {
        return self::make()
            ->title(self::getByKey('bulk_restore.title'))
            ->body(self::getByKey('bulk_restore.body'))
            ->success();
    }

    /**
     * Send a notification by key.
     */
    public static function send(string $key, array $replace = [], string $type = 'success'): Notification
    {
        $notification = self::make()
            ->title(self::getByKey("$key.title", $replace))
            ->body(self::getByKey("$key.body", $replace));

        return match ($type) {
            'info' => $notification->info(),
            'warning' => $notification->warning(),
            'danger' => $notification->danger(),
            default => $notification->success(),
        };
    }

    /**
     * Get dynamic message based on UX style.
     *
     * @deprecated Use getByKey('key') for centralized messages.
     */
    public static function getMessage(string $cheerful, string $formal): string
    {
        return self::getNotifStyle() === NotifStyle::Cheerful ? $cheerful : $formal;
    }

    /**
     * Get translation by key and current user notification style.
     */
    public static function getByKey(string $key, array $replace = []): string
    {
        $style = self::getNotifStyle()->value;

        return __("notif.{$style}.{$key}", $replace);
    }
}
