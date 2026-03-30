<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MyPendingAssignmentsTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Tugas Mendatang yang Belum Selesai';

    public static function canView(): bool
    {
        return auth()->user()?->student !== null;
    }

    public function table(Table $table): Table
    {
        $studentId = auth()->user()?->student?->id;

        return $table
            ->query(
                Assignment::query()
                    ->whereHas('assignmentTargets', function (Builder $query) use ($studentId) {
                        $query->where('student_id', $studentId)
                            ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                                $q->select('study_group_id')
                                    ->from('study_group_members')
                                    ->where('student_id', $studentId);
                            })
                            ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                                $q->select('id')
                                    ->from('study_groups')
                                    ->where('leader_id', $studentId);
                            });
                    })
                    ->whereDoesntHave('assignmentSubmissions', function (Builder $query) use ($studentId) {
                        $query->where('student_id', $studentId)
                            ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                                $q->select('study_group_id')
                                    ->from('study_group_members')
                                    ->where('student_id', $studentId);
                            })
                            ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                                $q->select('id')
                                    ->from('study_groups')
                                    ->where('leader_id', $studentId);
                            });
                    })
                    ->where('due_date', '>=', now())
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Tugas')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge(),

                TextColumn::make('due_date')
                    ->label('Tenggat Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->color(fn (Assignment $record) => $record->due_date < now()->addDays(2) ? 'danger' : 'warning'),
            ])
            ->recordActions([
                Action::make('kerjakan')
                    ->label('Lihat Detail & Kerjakan')
                    ->url(fn (Assignment $record): string => '/admin/learning/assignments/'.$record->id.'/submit')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->button()
                    ->color('primary'),
            ])
            ->emptyStateHeading('Tidak Ada Tugas')
            ->emptyStateDescription('Semua tugas telah diselesaikan atau tidak ada tugas mendatang.');
    }
}
