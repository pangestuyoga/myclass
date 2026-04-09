<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Exports\AttendanceExport;
use App\Models\ClassSession;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExportSessionAttendanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportAttendance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export Presensi')
            ->color('success')
            ->icon('heroicon-o-arrow-down-tray')
            ->link()
            ->action(function (array $arguments) {
                $session = ClassSession::findOrFail($arguments['session']);
                $courseName = $session->course?->name ?? '';

                $filename = 'rekap-presensi-'.Str::slug($courseName).'-sesi-'.$session->session_number.'-'.$session->date?->toDateString().'.xlsx';

                return Excel::download(new AttendanceExport($session), $filename);
            });
    }
}
