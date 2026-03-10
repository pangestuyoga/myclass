<?php

namespace App\Filament\Actions;

use App\Filament\Support\SystemNotification;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;

class DefaultBulkActions
{
    public static function make(string $entity): array
    {
        return [
            DeleteBulkAction::make()
                ->modalHeading("Hapus {$entity} yang dipilih")
                ->successNotification(
                    SystemNotification::bulkDelete()
                ),

            ForceDeleteBulkAction::make()
                ->modalHeading("Hapus {$entity} yang dipilih")
                ->successNotification(
                    SystemNotification::bulkForceDelete()
                ),

            RestoreBulkAction::make()
                ->modalHeading("Pulihkan {$entity} yang dipilih")
                ->successNotification(
                    SystemNotification::bulkRestore()
                ),
        ];
    }
}
