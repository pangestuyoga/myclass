<?php

namespace App\Filament\Resources\Master\Courses\Pages;

use App\Filament\Resources\Master\Courses\Actions\CreateCourseAction;
use App\Filament\Resources\Master\Courses\CourseResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCourses extends ManageRecords
{
    protected static string $resource = CourseResource::class;

    protected static ?string $title = 'Mata Kuliah';

    protected function getHeaderActions(): array
    {
        return [
            CreateCourseAction::make(),
        ];
    }
}
