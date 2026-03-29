<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Actions\Cheerful\DeleteAction;
use App\Models\ClassSession;

class DeleteSessionAction extends DeleteAction
{
    public static function getDefaultName(): ?string
    {
        return 'deleteSession';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Hapus')
            ->icon('heroicon-o-trash')
            ->link()
            ->record(fn (array $arguments) => ClassSession::find($arguments['session'], ['*']))
            ->color('danger');
    }
}
