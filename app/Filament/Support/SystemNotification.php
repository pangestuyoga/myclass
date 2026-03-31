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
     * Get the theme settings for the current user.
     */
    public static function getThemeSettings(): object
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $settings = $user?->settings;

        return (object) [
            'font' => $settings->font ?? 'Inter',
            'primary_color' => $settings->primary_color ?? 'amber',
            'border_radius' => match ($settings->border_radius ?? 'md') {
                'none' => '0px',
                'md' => '0.375rem',
                'lg' => '0.5rem',
                'xl' => '0.75rem',
                '2xl' => '1rem',
                default => '0.5rem',
            },
            'content_width' => $settings->content_width ?? 'full',
            'top_navigation' => $settings->top_navigation ?? false,
        ];
    }

    /**
     * Get the RGB values for the selected primary color.
     */
    public static function getPrimaryColorValues(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $colorName = $user?->settings?->primary_color ?? 'amber';

        // Mapping from Filament base colors (these are standard Tailwind scale RGB values)
        return match ($colorName) {
            'blue' => [
                50 => '239, 246, 255', 100 => '219, 234, 254', 200 => '191, 219, 254', 300 => '147, 197, 253',
                400 => '96, 165, 250', 500 => '59, 130, 246', 600 => '37, 99, 235', 700 => '29, 78, 216',
                800 => '30, 64, 175', 900 => '30, 58, 138', 950 => '23, 37, 84',
            ],
            'sky' => [
                50 => '240, 249, 255', 100 => '224, 242, 254', 200 => '186, 230, 253', 300 => '125, 211, 252',
                400 => '56, 189, 248', 500 => '14, 165, 233', 600 => '2, 132, 199', 700 => '3, 105, 161',
                800 => '7, 89, 133', 900 => '12, 74, 110', 950 => '8, 51, 68',
            ],
            'cyan' => [
                50 => '236, 254, 255', 100 => '207, 250, 254', 200 => '165, 243, 252', 300 => '103, 232, 249',
                400 => '34, 211, 238', 500 => '6, 182, 212', 600 => '8, 145, 178', 700 => '14, 116, 144',
                800 => '21, 94, 117', 900 => '22, 78, 99', 950 => '8, 51, 68',
            ],
            'emerald' => [
                50 => '236, 253, 245', 100 => '209, 250, 229', 200 => '167, 243, 208', 300 => '110, 231, 183',
                400 => '52, 211, 153', 500 => '16, 185, 129', 600 => '5, 150, 105', 700 => '4, 120, 87',
                800 => '6, 95, 70', 900 => '6, 77, 58', 950 => '2, 44, 34',
            ],
            'teal' => [
                50 => '240, 253, 250', 100 => '204, 251, 241', 200 => '153, 246, 228', 300 => '94, 234, 212',
                400 => '45, 212, 191', 500 => '20, 184, 166', 600 => '13, 148, 136', 700 => '15, 118, 110',
                800 => '17, 94, 89', 900 => '19, 78, 74', 950 => '4, 47, 46',
            ],
            'lime' => [
                50 => '247, 254, 231', 100 => '236, 252, 203', 200 => '217, 249, 157', 300 => '190, 242, 94',
                400 => '163, 230, 53', 500 => '132, 204, 22', 600 => '101, 163, 13', 700 => '77, 124, 15',
                800 => '63, 98, 18', 900 => '54, 83, 20', 950 => '26, 46, 5',
            ],
            'amber' => [
                50 => '255, 251, 235', 100 => '254, 243, 199', 200 => '253, 230, 138', 300 => '252, 211, 77',
                400 => '251, 191, 36', 500 => '245, 158, 11', 600 => '217, 119, 6', 700 => '180, 83, 9',
                800 => '146, 64, 14', 900 => '120, 53, 15', 950 => '69, 26, 3',
            ],
            'orange' => [
                50 => '255, 247, 237', 100 => '255, 237, 213', 200 => '254, 215, 170', 300 => '253, 186, 116',
                400 => '251, 146, 60', 500 => '249, 115, 22', 600 => '234, 88, 12', 700 => '194, 65, 12',
                800 => '154, 52, 18', 900 => '124, 45, 18', 950 => '67, 20, 7',
            ],
            'rose' => [
                50 => '255, 241, 242', 100 => '255, 228, 230', 200 => '254, 205, 211', 300 => '253, 164, 175',
                400 => '251, 113, 133', 500 => '244, 63, 94', 600 => '225, 29, 72', 700 => '190, 18, 60',
                800 => '159, 18, 57', 900 => '136, 19, 55', 950 => '76, 5, 25',
            ],
            'fuchsia' => [
                50 => '253, 244, 255', 100 => '245, 208, 254', 200 => '240, 171, 252', 300 => '232, 121, 249',
                400 => '217, 70, 239', 500 => '192, 38, 211', 600 => '162, 28, 175', 700 => '134, 25, 143',
                800 => '112, 26, 117', 900 => '86, 28, 94', 950 => '70, 10, 73',
            ],
            'violet' => [
                50 => '245, 243, 255', 100 => '237, 233, 254', 200 => '221, 214, 254', 300 => '196, 181, 253',
                400 => '167, 139, 250', 500 => '139, 92, 246', 600 => '124, 58, 237', 700 => '109, 40, 217',
                800 => '91, 33, 182', 900 => '76, 29, 149', 950 => '46, 16, 101',
            ],
            'indigo' => [
                50 => '238, 242, 255', 100 => '224, 231, 255', 200 => '199, 210, 254', 300 => '165, 180, 252',
                400 => '129, 140, 248', 500 => '99, 102, 241', 600 => '79, 70, 229', 700 => '67, 56, 202',
                800 => '55, 48, 163', 900 => '49, 46, 129', 950 => '30, 27, 75',
            ],
            default => [
                50 => '255, 251, 235', 100 => '254, 243, 199', 200 => '253, 230, 138', 300 => '252, 211, 77',
                400 => '251, 191, 36', 500 => '245, 158, 11', 600 => '217, 119, 6', 700 => '180, 83, 9',
                800 => '146, 64, 14', 900 => '120, 53, 15', 950 => '69, 26, 3',
            ],
        };
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
