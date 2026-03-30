<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Filament\Actions\Cheerful\CreateAction;

class CreateAssignmentAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'createAssignment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tambah');
    }
}
