<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Actions\Cheerful\EditAction;
use App\Models\ClassSession;
use Filament\Support\Enums\Width;

class EditSessionAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'editSession';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Ubah')
            ->color('warning')
            ->icon('heroicon-o-pencil')
            ->link()
            ->record(fn (array $arguments) => ClassSession::find($arguments['session'], ['*']))
            ->schema(function ($schema, $livewire) {
                return $livewire->form($schema);
            })
            ->modalWidth(Width::TwoExtraLarge);
    }
}
