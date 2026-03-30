<?php

namespace App\Filament\Resources\Master\Students\Actions;

use App\Filament\Actions\Cheerful\CreateAction;

class CreateStudentAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'createStudent';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tambah');
    }
}
