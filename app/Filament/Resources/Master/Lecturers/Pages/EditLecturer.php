<?php

namespace App\Filament\Resources\Master\Lecturers\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Resources\Master\Lecturers\LecturerResource;
use App\Filament\Support\SystemNotification;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLecturer extends EditRecord
{
    protected static string $resource = LecturerResource::class;

    protected ?string $heading = 'Ubah Dosen';

    protected static ?string $title = 'Ubah Dosen';

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ListLecturers::getUrl()),
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
