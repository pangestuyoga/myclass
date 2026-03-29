<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;

class ViewAssignmentsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewAssignments';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tugas')
            ->color('info')
            ->icon('heroicon-o-clipboard-document-list')
            ->link()
            ->modalHeading(fn (array $arguments) => 'Tugas Kuliah - Sesi Ke-'.ClassSession::find($arguments['session'], ['*'])->session_number)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::Large)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.assignments-modal', [
                'assignments' => ClassSession::find($arguments['session'], ['*'])->assignments()->latest()->get(),
            ]));
    }
}
