<?php

namespace App\Filament\Exports;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithEvents, WithHeadings, WithMapping, WithStyles
{
    protected ClassSession $session;

    protected Collection $activeStudents;

    protected Collection $attendanceRecords;

    public function __construct(ClassSession $session)
    {
        $session->load('course');
        $this->session = $session;
        $this->activeStudents = Student::whereHas('user', fn ($q) => $q->active())
            ->orderBy('full_name')
            ->get();

        $this->attendanceRecords = Attendance::query()
            ->whereHas('courseSchedule', function ($query) {
                $query->where('course_id', $this->session->course_id);
            })
            ->whereDate('date', $this->session->date)
            ->get()
            ->keyBy('student_id');
    }

    public function collection()
    {
        return $this->activeStudents->map(fn ($student) => (object) [
            'full_name' => $student->full_name,
            'student_number' => $student->student_number,
            'status' => $this->attendanceRecords->has($student->id) ? 'Hadir' : 'Alpa',
            'attended_at' => $this->attendanceRecords->get($student->id)?->attended_at,
        ]);
    }

    public function headings(): array
    {
        return [
            ['Data Presensi '.($this->session->course?->name ?? '').' Sesi Ke-'.$this->session->session_number.' ('.$this->session->date?->translatedFormat('d F Y').')'],
            ['Mahasiswa', 'NIM', 'Status', 'Waktu Presensi'],
        ];
    }

    public function map($row): array
    {
        return [
            $row->full_name,
            $row->student_number,
            $row->status,
            $row->attended_at ? $row->attended_at->translatedFormat('d F Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();

                $sheet->mergeCells("A1:{$lastColumn}1");

                $sheet->getStyle("A2:{$lastColumn}".$sheet->getHighestRow())
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_TOP)
                    ->setWrapText(true);

                foreach (range('A', $lastColumn) as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}
