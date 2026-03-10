<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;

class BackAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'back';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Kembali')
            ->color('secondary')
            ->outlined();
    }
}
