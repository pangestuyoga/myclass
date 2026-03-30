<?php

namespace App\Filament\Resources\Master\Students\Actions;

use App\Filament\Actions\Cheerful\EditAction;
use Filament\Support\Enums\Width;

class EditStudentAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'editStudent';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth(Width::Large);
    }
}
