<?php

namespace App\Filament\Pages\Learning\StudyGroup\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class SelectAllCoursesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'selectAllCourse';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon('heroicon-m-check-circle')
            ->tooltip('Pilih/Hapus Semua Mata Kuliah')
            ->action(function (Select $component) {
                $options = array_keys($component->getOptions());
                $state = $component->getState() ?? [];

                if (count($state) === count($options)) {
                    $component->state([]);
                } else {
                    $component->state($options);
                }
            });
    }
}
