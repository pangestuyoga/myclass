<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Actions\Cheerful\CreateAction;
use Filament\Support\Enums\Width;

class CreateSessionAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'createSession';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tambah')
            ->modalHeading('Tambah Sesi')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->schema(function ($schema, $livewire) {
                return $livewire->form($schema);
            })
            ->mutateDataUsing(function (array $data, $livewire): array {
                $data['course_id'] = $livewire->courseId;

                return $data;
            })
            ->extraModalFooterActions(fn (CreateAction $action): array => [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label('Simpan dan Tambah Lagi'),
            ])
            ->modalWidth(Width::TwoExtraLarge);
    }
}
