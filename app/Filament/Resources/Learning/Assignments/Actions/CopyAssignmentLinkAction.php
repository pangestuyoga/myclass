<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

class CopyAssignmentLinkAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'copyAssignmentLink';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Salin Tautan')
            ->color('gray')
            ->icon('heroicon-o-clipboard-document')
            ->action(function (array $arguments, $livewire) {
                $assignment = Assignment::find($arguments['record'] ?? null);
                $course = $assignment ? $assignment->course : null;

                if (! $course || ! $assignment) {
                    return;
                }

                $url = URL::temporarySignedRoute('share.assignment', now()->addHour(), [
                    'course' => $course->id,
                    'assignment_id' => $assignment->id,
                ]);

                $escapedUrl = json_encode($url);

                $livewire->js("if (navigator.clipboard) { navigator.clipboard.writeText({$escapedUrl}).catch(() => {}); }");

                SystemNotification::send('link_copied')
                    ->send();
            });
    }
}
