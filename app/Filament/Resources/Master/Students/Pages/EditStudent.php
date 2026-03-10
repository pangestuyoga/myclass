<?php

namespace App\Filament\Resources\Master\Students\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Resources\Master\Students\StudentResource;
use App\Filament\Support\SystemNotification;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected ?string $heading = 'Ubah Mahasiswa';

    protected static ?string $title = 'Ubah Mahasiswa';

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ListStudents::getUrl()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return SystemNotification::update();
    }
}
