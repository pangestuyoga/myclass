<?php

namespace App\Filament\Actions\Cheerful;

use App\Filament\Support\SystemNotification;
use Filament\Actions\CreateAction as ActionsCreateAction;

class CreateAction extends ActionsCreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotification(
            SystemNotification::create()
        );
    }
}
