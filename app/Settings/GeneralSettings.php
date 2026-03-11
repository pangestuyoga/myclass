<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $current_semester;

    public static function group(): string
    {
        return 'general';
    }
}
