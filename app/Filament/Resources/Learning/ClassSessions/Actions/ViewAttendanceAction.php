<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;

class ViewAttendanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewAttendance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Presensi')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->link()
            ->modalHeading(fn (array $arguments) => 'Daftar Presensi - Sesi Ke-'.ClassSession::find($arguments['session'], ['*'])->session_number)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::ExtraLarge)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.attendance-modal', [
                'attendances' => ClassSession::find($arguments['session'], ['*'])->attendances()->with('student')->latest('attended_at')->get(),
            ]));
    }
}
