<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Models\Student;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;

class SelectAllStudentsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'selectAll';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Pilih Semua')
            ->action(function (Set $set) {
                $studentIds = Student::pluck('id')->toArray();
                $set('student_ids', $studentIds);
            });
    }
}
