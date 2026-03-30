<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NotifStyle: string implements HasLabel
{
    case Cheerful = 'cheerful';
    case Formal = 'formal';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cheerful => 'Ceria (Banyak Emoji) 🚀✨',
            self::Formal => 'Formal & Profesional 💼',
        };
    }
}
