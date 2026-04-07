<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Models\Assignment;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;

class ShareAssignmentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'shareAssignment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Bagikan Tugas')
            ->visible(fn () => auth()->user()->can('Share:Assignment'))
            ->icon('heroicon-o-paper-airplane')
            ->color(Color::Fuchsia)
            ->link()
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-paper-airplane')
            ->modalHeading('Bagikan Info Tugas')
            ->modalDescription('Pilih metode untuk membagikan tautan publik status pengumpulan tugas ini.')
            ->modalSubmitActionLabel('Ke WhatsApp')
            ->modalCancelAction(false)
            ->extraModalFooterActions(fn (Action $action): array => [
                CopyAssignmentLinkAction::make()
                    ->arguments($action->getArguments()),
            ])
            ->action(function (array $arguments, $livewire) {
                $assignment = Assignment::with(['course'])->find($arguments['record'] ?? null);
                $course = $assignment ? $assignment->course : null;

                if (! $course || ! $assignment) {
                    return;
                }

                $url = URL::temporarySignedRoute('share.assignment', now()->addHour(), [
                    'course' => $course->id,
                    'assignment_id' => $assignment->id,
                ]);

                $text = "*Info Tugas Kelas {$course->name}*\nJudul: {$assignment->title}\n\nSilakan cek status pengumpulan tugas melalui tautan publik berikut:\n\n{$url}";

                $escapedText = json_encode($text);

                $livewire->js(
                    <<<JS
const text = {$escapedText};
const url = 'https://wa.me/?text=' + encodeURIComponent(text);
window.open(url, '_blank');
JS
                );
            });
    }
}
