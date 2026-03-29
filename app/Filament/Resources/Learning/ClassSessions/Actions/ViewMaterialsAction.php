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
            ->modalHeading(fn (array $arguments) => 'Materi Kuliah - Sesi Ke-'.(ClassSession::find($arguments['session'] ?? null, ['*'])?->session_number ?? ''))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::Large)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.materials-modal', [
                'materials' => ClassSession::find($arguments['session'] ?? null, ['*'])?->materials()
                    ->latest()
                    ->get()
                    ->map(fn ($m) => (object) [
                        'id' => $m->id,
                        'title' => $m->title,
                        'created_at_formatted' => $m->created_at?->translatedFormat('d F Y') ?? '-',
                    ]) ?? collect(),
            ]));
    }
}
