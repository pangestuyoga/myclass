<?php

namespace App\Filament\Pages\Learning\StudyGroup\Actions;

use App\Filament\Actions\Cheerful\EditAction;
use App\Models\StudyGroup;
use Filament\Support\Enums\Width;

class EditStudyGroupAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'editStudyGroup';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Ubah')
            ->color('warning')
            ->icon('heroicon-o-pencil-square')
            ->link()
            ->tooltip('Ubah')
            ->record(fn (array $arguments): StudyGroup => StudyGroup::findOrFail($arguments['studyGroup']))
            ->modalHeading(fn (StudyGroup $record) => "Ubah {$record->name}")
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalWidth(Width::ThreeExtraLarge)
            ->schema(fn ($livewire) => $livewire->studyGroupFormSchema())
            ->fillForm(function (StudyGroup $record): array {
                return [
                    'name' => $record->name,
                    'leader_id' => $record->leader_id,
                    'course_id' => $record->courses->pluck('id')->toArray(),
                    'students' => $record->students->pluck('id')->toArray(),
                ];
            })
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
