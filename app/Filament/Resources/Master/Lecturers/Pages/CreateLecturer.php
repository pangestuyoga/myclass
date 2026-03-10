<?php

namespace App\Filament\Resources\Master\Lecturers\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Resources\Master\Lecturers\LecturerResource;
use App\Filament\Support\SystemNotification;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateLecturer extends CreateRecord
{
    protected static string $resource = LecturerResource::class;

    protected ?string $heading = 'Tambah Dosen';

    protected static ?string $title = 'Tambah Dosen';

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

    protected function getCreatedNotification(): ?Notification
    {
        return SystemNotification::create();
    }

    public function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make('Minimal8@'),
            ]);

            unset($data['email'], $data['username']);

            return $this->getResource()::getModel()::create(
                array_merge($data, [
                    'user_id' => $user->id,
                ])
            );
        });
    }
}
