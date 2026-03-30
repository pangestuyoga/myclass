<?php

namespace App\Filament\Columns;

use Filament\Tables\Columns\TextColumn;

class RowIndexColumn
{
    public static function make(): array
    {
        return [
            TextColumn::make('index')
                ->label('No.')
                ->rowIndex()
                ->width('10px'),
        ];
    }
}
