<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\Material;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;

class ViewMaterialDetailAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewMaterialDetail';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->record(fn (array $arguments) => Material::find($arguments['record'] ?? null, ['*']))
            ->modalHeading('Detail Materi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::FourExtraLarge)
            ->modalContent(fn (?Material $record) => $record ? view('filament.resources.learning.class-sessions.material-detail-modal', [
                'record' => $record,
            ]) : null);
    }
}
