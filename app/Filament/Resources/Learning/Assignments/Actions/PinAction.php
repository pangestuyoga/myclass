<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\AssignmentPin;
use Filament\Actions\Action;

class PinAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'pin';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(fn (array $arguments, $livewire) => in_array($arguments['record'], $livewire->pinnedIds) ? 'heroicon-s-bookmark' : 'heroicon-o-bookmark')
            ->color(fn (array $arguments, $livewire) => in_array($arguments['record'], $livewire->pinnedIds) ? 'primary' : 'gray')
            ->tooltip(fn (array $arguments, $livewire) => in_array($arguments['record'], $livewire->pinnedIds) ? 'Lepas pin' : 'Pin tugas ini')
            ->size('sm')
            ->iconButton()
            ->action(function (array $arguments, $livewire) {
                $assignmentId = $arguments['record'];
                $studentProfile = auth()->user()->student;

                $existing = AssignmentPin::where('student_id', $studentProfile->id)
                    ->where('assignment_id', $assignmentId)
                    ->first();

                if ($existing) {
                    $existing->delete();
                    SystemNotification::success('Pin Tugas Dilepas 📌', 'Pin pada tugas ini telah berhasil dilepas dari daftar prioritas Anda.')->send();
                } else {
                    AssignmentPin::create([
                        'student_id' => $studentProfile->id,
                        'assignment_id' => $assignmentId,
                    ]);
                    SystemNotification::success('Tugas Berhasil Di-pin 📍', 'Tugas ini sekarang berada di posisi teratas daftar prioritas Anda.')->send();
                }

                unset($livewire->assignments, $livewire->pinnedIds);
            });
    }
}
