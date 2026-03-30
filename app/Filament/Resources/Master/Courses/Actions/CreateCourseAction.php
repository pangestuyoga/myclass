<?php

namespace App\Filament\Resources\Master\Courses\Actions;

use App\Filament\Actions\Cheerful\CreateAction;
use Filament\Support\Enums\Width;

class CreateCourseAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'createCourse';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tambah')
            ->modalHeading('Tambah Mata Kuliah')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->extraModalFooterActions(fn (CreateAction $action): array => [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label('Simpan dan Tambah Lagi'),
            ])
            ->modalWidth(Width::Large);
    }
}
