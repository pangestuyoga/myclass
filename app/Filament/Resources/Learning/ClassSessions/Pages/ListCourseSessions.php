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

class ListCourseSessions extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected static string $resource = ClassSessionResource::class;

    protected string $view = 'filament.resources.learning.class-sessions.pages.list-course-sessions';

    public $courseId;

    public function mount($courseId): void
    {
        $this->courseId = $courseId;
    }

    public function getCourse(): Course
    {
        return Course::findOrFail($this->courseId);
    }

    public function getSessions(): Collection
    {
        return $this->getCourse()->classSessions()->orderBy('session_number', 'asc')->get();
    }

    public function getTitle(): string
    {
        return 'Sesi Kelas - '.$this->getCourse()->name;
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
