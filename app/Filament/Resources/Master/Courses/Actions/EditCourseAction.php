<?php

namespace App\Filament\Resources\Master\Courses\Actions;

use App\Filament\Actions\Cheerful\EditAction;
use Filament\Support\Enums\Width;

class EditCourseAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'editCourse';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth(Width::Large);
    }
}
