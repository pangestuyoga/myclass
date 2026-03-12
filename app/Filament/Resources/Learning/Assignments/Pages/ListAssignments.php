<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Resources\Learning\Assignments\Actions\DeleteAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\EditAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\PinAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Models\Assignment;
use App\Models\AssignmentPin;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

class ListAssignments extends Page
{
    protected static string $resource = AssignmentResource::class;

    protected string $view = 'filament.resources.learning.assignments.pages.list-assignments';

    protected static ?string $title = 'Tugas';

    protected static ?string $recordTitleAttribute = 'title';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah'),
        ];
    }

    public function pinAction(): Action
    {
        return PinAction::make();
    }

    public function editAssignmentAction(): Action
    {
        return EditAssignmentAction::make()
            ->visible(fn () => auth()->user()->can('Update:Assignment'));
    }

    public function deleteAssignmentAction(): Action
    {
        return DeleteAssignmentAction::make()
            ->visible(fn () => auth()->user()->can('Delete:Assignment'));
    }

    #[Computed]
    public function assignments(): Collection
    {
        $studentProfile = auth()->user()->student;

        $pinnedIds = $this->pinnedIds;

        return Assignment::with(['course', 'assignmentSubmissions' => function ($q) use ($studentProfile) {
            $q->where('student_id', $studentProfile->id)
                ->orWhereHas('studyGroup', fn ($sq) => $sq->whereHas('students', fn ($ssq) => $ssq->whereKey($studentProfile->id)));
        }, 'studyGroups.students'])
            ->whereHas('course', function ($q) {
                $q->where('semester', app(GeneralSettings::class)->current_semester);
            })
            ->where(function ($query) use ($studentProfile) {
                $query->whereHas('students', fn ($q) => $q->whereKey($studentProfile->id))
                    ->orWhereHas('studyGroups', fn ($q) => $q->whereHas('students', fn ($sq) => $sq->whereKey($studentProfile->id)));
            })
            ->when(! empty($pinnedIds), function ($query) use ($pinnedIds) {
                $ids = implode(',', $pinnedIds);
                $query->orderByRaw("FIELD(id, $ids) DESC");
            })
            ->orderBy('due_date')
            ->get();
    }

    #[Computed]
    public function assignmentCards()
    {
        $studentProfile = auth()->user()->student;
        $pinnedIds = $this->pinnedIds;

        return $this->assignments()->map(function ($assignment) use ($studentProfile, $pinnedIds) {
            $submission = $assignment->assignmentSubmissions->first();
            $isSubmitted = $submission !== null;
            $isGroup = $assignment->type === AssignmentType::Group;

            $isLeader = false;
            if ($isGroup) {
                $userGroup = $assignment->studyGroups->first(function ($g) use ($studentProfile) {
                    return $g->students->contains($studentProfile->id);
                });
                $isLeader = $userGroup && $userGroup->leader_id === $studentProfile->id;
            }

            $isOverdue = now()->isAfter($assignment->due_date);
            $canSubmit = ! $isOverdue;

            $canSubmitByRole = ! $isGroup || $isLeader;
            $canSubmitActual = $canSubmit && $canSubmitByRole;

            $isUrgent = ! $isSubmitted && $canSubmitActual && now()->diffInHours($assignment->due_date) <= 48;
            $isNew = $assignment->created_at->diffInDays(now()) <= 3;
            $isPinned = in_array($assignment->id, $pinnedIds);

            $statusLabel = 'Belum Dikumpulkan';
            $statusColor = 'warning';
            $statusIcon = 'heroicon-o-arrow-up-tray';

            if ($isSubmitted) {
                $statusLabel = '✓ Sudah Dikumpulkan';
                $statusColor = 'success';
                $statusIcon = 'heroicon-o-check-circle';
            } elseif ($isOverdue) {
                $statusLabel = '⏰ Waktu Habis';
                $statusColor = 'danger';
                $statusIcon = 'heroicon-o-clock';
            } elseif ($isGroup && ! $isLeader && ! $isSubmitted) {
                $statusLabel = 'Menunggu Ketua';
                $statusColor = 'gray';
                $statusIcon = 'heroicon-o-user-group';
            }

            return (object) [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'course_name' => $assignment->course?->name ?? '-',
                'due_date_formatted' => $assignment->due_date->translatedFormat('l, d F Y, H:i'),
                'submitted_at_formatted' => ($isSubmitted && $submission->submitted_at) ? $submission->submitted_at->translatedFormat('l, d F Y, H:i') : null,
                'is_submitted' => $isSubmitted,
                'is_group' => $isGroup,
                'is_leader' => $isLeader,
                'is_overdue' => $isOverdue,
                'can_submit_actual' => $canSubmitActual,
                'is_urgent' => $isUrgent,
                'is_new' => $isNew,
                'is_pinned' => $isPinned,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'status_icon' => $statusIcon,
            ];
        });
    }

    #[Computed]
    public function pinnedIds(): array
    {
        $studentProfile = auth()->user()->student;

        return AssignmentPin::where('student_id', $studentProfile->id)
            ->pluck('assignment_id')
            ->toArray();
    }
}
