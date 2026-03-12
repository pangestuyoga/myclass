<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoleEnum: string implements HasLabel
{
    case Developer = 'Developer';
    case Student = 'Mahasiswa';
    case Kosma = 'Kosma';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Developer => 'Developer',
            self::Student => 'Mahasiswa',
            self::Kosma => 'Kosma',
        };
    }
}
