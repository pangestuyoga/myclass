<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SupportWidget extends Widget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.support-widget';
}
