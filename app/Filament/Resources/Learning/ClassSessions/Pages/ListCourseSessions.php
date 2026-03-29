<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\Course;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ListCourseSessions extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected static string $resource = ClassSessionResource::class;

    protected string $view = 'filament.resources.learning.class-sessions.index';

    public $courseId;

    public function mount($courseId): void
    {
        $this->courseId = $courseId;
    }

    #[Computed]
    public function course(): Course
    {
        return Course::findOrFail($this->courseId);
    }

    #[Computed]
    public function sessions(): Collection
    {
        return $this->course->classSessions()
            ->orderBy('session_number', 'asc')
            ->get()
            ->map(fn ($session) => (object) [
                'id' => $session->id,
                'session_number' => $session->session_number,
                'date_formatted' => $session->date->format('l, d F Y'),
                'time_range' => $session->start_time->format('H:i').' - '.$session->end_time->format('H:i'),
            ]);
    }

    #[Computed]
    public function description(): string
    {
        return 'Data sesi pembelajaran untuk mata kuliah '.$this->course->name;
    }

    public function getTitle(): string
    {
        return 'Sesi Kelas - '.$this->course->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Sesi')
                ->modalHeading('Tambah Sesi')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['course_id'] = $this->courseId;

                    return $data;
                })
                ->extraModalFooterActions(fn (CreateAction $action): array => [
                    $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                        ->label('Simpan dan Tambah Lagi'),
                ])
                ->modalWidth(Width::TwoExtraLarge),
        ];
    }

    public function editSessionAction(): Action
    {
        return EditAction::make('editSession')
            ->record(fn (array $arguments) => ClassSession::find($arguments['session']))
            ->modalWidth(Width::TwoExtraLarge);
    }

    public function deleteSessionAction(): Action
    {
        return DeleteAction::make('deleteSession')
            ->record(fn (array $arguments) => ClassSession::find($arguments['session']));
    }
}
