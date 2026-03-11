<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Enums\AssignmentType;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Models\Assignment;
use Filament\Actions\Action;

class SubmitAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'submit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Kumpulkan')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->url(fn (Assignment $record) => AssignmentResource::getUrl('submit', ['record' => $record->id]))
            ->visible(function (Assignment $record) {
                if (now()->isAfter($record->due_date)) {
                    return false;
                }

                if ($record->type === AssignmentType::Group) {
                    return $record->studyGroups()
                        ->where('leader_id', auth()->user()->student->id)
                        ->exists();
                }

                return true;
            });
    }
}
