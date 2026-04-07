<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use Filament\Actions\Action;

class MarkAsSentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markAsSentAction';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(function (array $arguments, ?Assignment $record) {
            $assignment = $record ?? Assignment::find($arguments['record'] ?? null);

            return $assignment?->is_sent_to_lecturer ? 'Buka Kunci' : 'Tandai Terkirim';
        })
            ->icon(function (array $arguments, ?Assignment $record) {
                $assignment = $record ?? Assignment::find($arguments['record'] ?? null);

                return $assignment?->is_sent_to_lecturer ? 'heroicon-o-lock-open' : 'heroicon-o-check-circle';
            })
            ->color(function (array $arguments, ?Assignment $record) {
                $assignment = $record ?? Assignment::find($arguments['record'] ?? null);

                return $assignment?->is_sent_to_lecturer ? 'warning' : 'success';
            })
            ->link()
            ->visible(fn () => auth()->user()->hasRole(['Kosma', 'Developer']))
            ->action(function (array $arguments, $livewire, ?Assignment $record) {
                $assignment = $record ?? Assignment::find($arguments['record'] ?? null);
                if (! $assignment) {
                    return;
                }

                $assignment->update([
                    'is_sent_to_lecturer' => ! $assignment->is_sent_to_lecturer,
                ]);

                SystemNotification::send($assignment->is_sent_to_lecturer ? 'assignment_sent' : 'assignment_unlocked')
                    ->send();

                if (isset($livewire->assignments)) {
                    unset($livewire->assignments);
                }
            });
    }
}
