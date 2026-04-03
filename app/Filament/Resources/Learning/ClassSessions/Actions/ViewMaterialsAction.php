<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
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
            ->color(Color::Purple)
            ->icon('heroicon-o-book-open')
            ->outlined()
            ->modalHeading('Materi Sesi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::FourExtraLarge)
            ->modalContent(function (array $arguments) {
                $material = ClassSession::find($arguments['session'] ?? null)?->materials()->first();

                return view('filament.resources.learning.class-sessions.material-modal', ['record' => $material]);
            });
    }
}
