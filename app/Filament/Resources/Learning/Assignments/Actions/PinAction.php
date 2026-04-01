<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\AssignmentPin;
use App\Models\User;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class PinAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'pin';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (array $arguments, $livewire) => in_array($arguments['record'], $livewire->pinnedIds) ? 'Lepas Pin' : 'Pin Tugas')
            ->color(fn (array $arguments, $livewire) => in_array($arguments['record'], $livewire->pinnedIds) ? 'primary' : 'gray')
            ->icon(fn (array $arguments, $livewire) => in_array($arguments['record'], $livewire->pinnedIds) ? 'heroicon-s-bookmark' : 'heroicon-o-bookmark')

            ->link()
            ->tooltip(fn (array $arguments, $livewire) => in_array($arguments['record'], $livewire->pinnedIds) ? 'Lepas pin' : 'Pin tugas ini')
            ->action(function (array $arguments, $livewire) {
                $assignmentId = $arguments['record'];
                /** @var User|null $user */
                $user = Auth::user();
                $studentProfile = $user?->student;

                if (! $studentProfile) {
                    return;
                }

                $existing = AssignmentPin::where('student_id', $studentProfile->id)
                    ->where('assignment_id', $assignmentId)
                    ->first();

                if ($existing) {
                    $existing->delete();
                    SystemNotification::send('assignment_unpinned')
                        ->send();
                } else {
                    AssignmentPin::create([
                        'student_id' => $studentProfile->id,
                        'assignment_id' => $assignmentId,
                    ]);

                    SystemNotification::send('assignment_pinned')
                        ->send();
                }

                unset($livewire->assignments, $livewire->pinnedIds);
            });
    }
}
