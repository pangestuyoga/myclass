<?php

namespace App\Filament\Actions\Cheerful;

use App\Filament\Support\SystemNotification;
use Filament\Actions\DeleteAction as ActionsDeleteAction;

class DeleteAction extends ActionsDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotification(
            SystemNotification::delete()
        );
    }
}
