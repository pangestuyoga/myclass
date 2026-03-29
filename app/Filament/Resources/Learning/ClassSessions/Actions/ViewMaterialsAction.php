<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;

class ViewMaterialsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewMaterials';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Materi')
            ->color('warning')
            ->icon('heroicon-o-book-open')
            ->link()
            ->modalHeading(fn (array $arguments) => 'Materi Kuliah - Sesi Ke-'.ClassSession::find($arguments['session'], ['*'])->session_number)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::Large)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.materials-modal', [
                'materials' => ClassSession::find($arguments['session'], ['*'])->materials()->latest()->get(),
            ]));
    }
}
