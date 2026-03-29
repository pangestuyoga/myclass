<?php

namespace App\Filament\Pages\Learning\StudyGroup\Actions;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Models\StudyGroup;
use Filament\Support\Enums\Width;

class CreateStudyGroupAction extends CreateAction
{
    public static function getDefaultName(): string
    {
        return 'createStudyGroup';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->model(StudyGroup::class)
            ->label('Tambah')
            ->modalHeading('Tambah Kelompok Belajar')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->extraModalFooterActions(fn (CreateAction $action): array => [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label('Simpan dan Tambah Lagi'),
            ])
            ->modalWidth(Width::ThreeExtraLarge)
            ->schema(fn ($livewire) => $livewire->studyGroupFormSchema())
            ->after(function (StudyGroup $record, array $data) {
                if (isset($data['course_id'])) {
                    $record->courses()->sync($data['course_id']);
                }
                if (isset($data['students'])) {
                    $record->students()->sync($data['students']);
                }
            });
    }
}
