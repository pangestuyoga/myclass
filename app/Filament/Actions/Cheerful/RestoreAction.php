<?php

namespace App\Filament\Actions\Cheerful;

use App\Filament\Support\SystemNotification;
use Filament\Actions\RestoreAction as ActionsRestoreAction;

class RestoreAction extends ActionsRestoreAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotification(
            SystemNotification::restore()
        );
    }
}
