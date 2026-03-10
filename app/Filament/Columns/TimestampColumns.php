<?php

namespace App\Filament\Columns;

use Filament\Tables\Columns\TextColumn;

class TimestampColumns
{
    public static function make(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Dibuat')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime('l, d F Y H:i:s')
                ->wrap(),

            TextColumn::make('updated_at')
                ->label('Diperbarui')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime('l, d F Y H:i:s')
                ->wrap(),

            TextColumn::make('deleted_at')
                ->label('Dihapus')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime('l, d F Y H:i:s')
                ->wrap(),
        ];
    }
}
