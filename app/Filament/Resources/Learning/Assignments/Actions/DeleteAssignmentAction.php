<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Filament\Actions\Cheerful\DeleteAction;
use App\Models\Assignment;

class DeleteAssignmentAction extends DeleteAction
{
    public static function getDefaultName(): ?string
    {
        return 'deleteAssignment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Hapus')
            ->icon('heroicon-o-trash')
            ->link()
            ->modalHeading(fn (Assignment $record) => "Hapus {$record->title}")
            ->record(fn (array $arguments) => Assignment::find($arguments['record']))
            ->after(function ($livewire) {
                unset($livewire->assignments);
            })
            ->color('danger')
            ->tooltip('Hapus');
    }
}
