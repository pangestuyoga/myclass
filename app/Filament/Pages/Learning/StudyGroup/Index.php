<?php

namespace App\Filament\Pages\Learning\StudyGroup;

use App\Filament\Pages\Learning\StudyGroup\Actions\CreateStudyGroupAction;
use App\Filament\Pages\Learning\StudyGroup\Actions\DeleteStudyGroupAction;
use App\Filament\Pages\Learning\StudyGroup\Actions\EditStudyGroupAction;
use App\Filament\Pages\Learning\StudyGroup\Actions\SelectAllCoursesAction;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Settings\GeneralSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use UnitEnum;

class Index extends Page implements HasActions, HasForms
{
    use HasPageShield, InteractsWithActions, InteractsWithForms;

    protected static ?string $model = StudyGroup::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $title = 'Kelompok Belajar';

    protected static ?int $navigationSort = 25;

    protected static ?string $slug = 'learning/study-groups';

    public static function getPagePermission(): string
    {
        return 'View:StudyGroup';
    }

    protected string $view = 'filament.pages.learning.study-group.index';

    public ?int $course_id = null;

    public function mount(): void
    {
        $this->form->fill([
            'course_id' => $this->course_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('course_id')
                    ->label('Mata Kuliah')
                    ->placeholder('Semua Mata Kuliah')
                    ->options(function () {
                        return Course::query()
                            ->where('semester', app(GeneralSettings::class)->current_semester)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->live(),

            ])
            ->columns([
                'sm' => 1,
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateStudyGroupAction::make()
                ->visible(fn () => auth()->user()->can('Create:StudyGroup')),
        ];
    }

    public function editStudyGroupAction(): Action
    {
        return EditStudyGroupAction::make()
            ->visible(fn () => auth()->user()->can('Update:StudyGroup'));
    }

    public function deleteStudyGroupAction(): Action
    {
        return DeleteStudyGroupAction::make()
            ->visible(fn () => auth()->user()->can('Delete:StudyGroup'));
    }

    #[Computed]
    public function studyGroups(): Collection
    {
        $query = StudyGroup::with(['leader', 'students', 'courses'])
            ->whereHas('courses', fn ($q) => $q->where('courses.semester', app(GeneralSettings::class)->current_semester));

        if ($this->course_id) {
            $query->whereHas('courses', fn ($q) => $q->where('courses.id', $this->course_id));
        }

        $studentId = auth()->user()?->student?->id;

        return $query->latest()->get()->map(function ($record) use ($studentId) {
            $isMyGroup = ($studentId && ($record->leader_id === $studentId || $record->students->contains($studentId)));

            return (object) [
                'id' => $record->id,
                'name' => $record->name,
                'is_my_group' => $isMyGroup,
                'leader_avatar' => $record->leader?->user?->facehash_avatar_url,
                'leader_name' => $record->leader->full_name ?? 'Belum Ditentukan',
                'is_leader' => $studentId && $record->leader_id === $studentId,
                'courses' => $record->courses->map(fn ($c) => (object) [
                    'name' => $c->name,
                ]),
                'students_count' => $record->students->count(),
                'students' => $record->students->map(fn ($s) => (object) [
                    'id' => $s->id,
                    'full_name' => $s->full_name,
                    'is_me' => $studentId && $s->id === $studentId,
                ]),
                // Pre-calculated classes
                'card_classes' => Arr::toCssClasses([
                    'fi-card flex flex-col justify-between rounded-xl border transition duration-200 group relative',
                    'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md' => ! $isMyGroup,
                    'bg-primary-50/30 dark:bg-primary-900/10 border-primary-500 shadow-md ring-1 ring-primary-500' => $isMyGroup,
                ]),
            ];
        });
    }

    public function isMyGroup(StudyGroup $record): bool
    {
        $studentId = auth()->user()?->student?->id;

        if (! $studentId) {
            return false;
        }

        return $record->leader_id === $studentId || $record->students->contains($studentId);
    }

    public function studyGroupFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('course_id')
                        ->label('Mata Kuliah')
                        ->options(function () {
                            return Course::query()
                                ->where('semester', app(GeneralSettings::class)->current_semester)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->multiple()
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpanFull()
                        ->suffixAction(SelectAllCoursesAction::make()),

                    TextInput::make('name')
                        ->label('Nama Kelompok')
                        ->placeholder('Kelompok A / Kelompok 1')
                        ->required()
                        ->maxLength(100)
                        ->autocomplete(false),

                    Select::make('leader_id')
                        ->label('Ketua Kelompok')
                        ->options(function (Get $get, ?StudyGroup $record) {
                            $query = Student::query();

                            $courseIds = array_values(array_filter((array) ($get('course_id') ?? [])));
                            if (! empty($courseIds)) {
                                $allBusyIds = StudyGroup::getBusyStudentIdsForCourses(
                                    courseIds: $courseIds,
                                    excludeGroupId: $record?->id,
                                );
                                $selectedMembers = array_values(array_filter((array) ($get('students') ?? [])));
                                $query->whereNotIn('id', array_unique(array_merge($allBusyIds, $selectedMembers)));
                            }

                            return $query->get()->pluck('full_name', 'id')->toArray();
                        })
                        ->searchable()
                        ->required()
                        ->native(false)
                        ->live(),

                    Select::make('students')
                        ->label('Anggota Mahasiswa')
                        ->relationship(
                            name: 'students',
                            titleAttribute: 'full_name',
                            modifyQueryUsing: function (Builder $query, Get $get, ?StudyGroup $record) {

                                $courseIds = array_values(array_filter((array) ($get('course_id') ?? [])));
                                if (! empty($courseIds)) {
                                    $allBusyIds = StudyGroup::getBusyStudentIdsForCourses(
                                        courseIds: $courseIds,
                                        excludeGroupId: $record?->id,
                                    );
                                    $leaderId = $get('leader_id');
                                    if ($leaderId) {
                                        $allBusyIds[] = (int) $leaderId;
                                    }
                                    $query->whereNotIn(
                                        $query->getModel()->getTable().'.id',
                                        array_unique($allBusyIds)
                                    );
                                }

                                return $query->orderBy('student_number', 'asc');
                            }
                        )
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->live()
                        ->columnSpanFull()
                        ->required(),
                ]),
        ];
    }
}
