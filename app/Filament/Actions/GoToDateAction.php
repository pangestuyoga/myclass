<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\Width;

class GoToDateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'goToDate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Lompat ke Tanggal')
            ->icon('heroicon-o-calendar-days')
            ->schema([
                DatePicker::make('date')
                    ->label('Pilih Tanggal')
                    ->placeholder(fn () => now()->addMonth(10)->format('l, d F Y'))
                    ->required()
                    ->native(false)
                    ->displayFormat('l, d F Y'),
            ])
            ->action(function (array $data, $livewire) {
                $livewire->setOption('date', $data['date']);
            })
            ->modalWidth(Width::Large);
    }
}
