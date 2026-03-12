<?php

namespace App\Filament\Pages\Learning\StudyGroup;

use App\Filament\Pages\Learning\StudyGroup\Actions\CreateStudyGroupAction;
use App\Filament\Pages\Learning\StudyGroup\Actions\DeleteStudyGroupAction;
use App\Filament\Pages\Learning\StudyGroup\Actions\EditStudyGroupAction;
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
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class Index extends Page implements HasActions, HasForms
{
    use HasPageShield, InteractsWithActions, InteractsWithForms;

    protected static ?string $model = StudyGroup::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $title = 'Kelompok Belajar';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'learning/study-groups';

    public static function getPagePermission(): string
    {
        return 'View:StudyGroup';
    }

    protected string $view = 'filament.pages.information.list-study-groups';

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

    public function getStudyGroups(): Collection
    {
        $query = StudyGroup::with(['leader', 'students', 'courses'])
            ->whereHas('courses', fn ($q) => $q->where('courses.semester', app(GeneralSettings::class)->current_semester));

        if ($this->course_id) {
            $query->whereHas('courses', fn ($q) => $q->where('courses.id', $this->course_id));
        }

        return $query->latest()->get();
    }

    protected function getViewData(): array
    {
        return [
            'groups' => $this->getStudyGroups(),
            'studentId' => auth()->user()?->student?->id,
        ];
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
                        ->columnSpanFull(),

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
