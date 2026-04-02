<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ChangelogType: string implements HasColor, HasIcon, HasLabel
{
    case Feature = 'feature';
    case Improvement = 'improvement';
    case BugFix = 'bugfix';
    case Security = 'security';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Feature => 'Fitur Baru 🚀',
            self::Improvement => 'Peningkatan ✨',
            self::BugFix => 'Perbaikan Bug 🐛',
            self::Security => 'Keamanan 🔐',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Feature => 'success',
            self::Improvement => 'info',
            self::BugFix => 'danger',
            self::Security => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Feature => 'heroicon-o-rocket-launch',
            self::Improvement => 'heroicon-o-sparkles',
            self::BugFix => 'heroicon-o-bug-ant',
            self::Security => 'heroicon-o-shield-check',
        };
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::Feature => 'emerald',
            self::Improvement => 'blue',
            self::BugFix => 'rose',
            self::Security => 'amber',
        };
    }
}
