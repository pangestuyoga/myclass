<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.current_semester', 4);
        $this->migrator->add('general.kosma_id', null);
    }
};
