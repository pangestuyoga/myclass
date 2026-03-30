<?php

namespace App\Filament\Actions\Cheerful;

use App\Filament\Support\SystemNotification;
use Filament\Actions\EditAction as ActionsEditAction;

class EditAction extends ActionsEditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotification(
            SystemNotification::update()
        )
            ->color('warning')
            ->modalHeading('Ubah Data');
    }
}
