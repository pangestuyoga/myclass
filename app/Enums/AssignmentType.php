<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssignmentType: string implements HasLabel
{
    case Individual = 'Individual';
    case Group = 'Group';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Individual => 'Individu',
            self::Group => 'Kelompok',
        };
    }
}
