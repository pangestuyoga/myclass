<?php

namespace App\Http\Controllers;

use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShareAttendanceController extends Controller
{
    public function show(Request $request, Course $course): View
    {

        $latestAttendance = Attendance::whereHas('courseSchedule', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->latest('date')
            ->first();

        $defaultDate = $latestAttendance ? $latestAttendance->date?->toDateString() : now()->toDateString();
        $date = $request->query('date', $defaultDate);

        $attendances = Attendance::with('student')
            ->whereHas('courseSchedule', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->whereDate('date', $date)
            ->get();

        $availableDates = Attendance::whereHas('courseSchedule', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->get()
            ->pluck('date');

        $assignments = Assignment::where('course_id', $course->id)
            ->withCount('assignmentSubmissions')
            ->latest()
            ->get();

        $sessionInfo = ClassSession::where('course_id', $course->id)->whereDate('date', $date)->first();
        $totalStudents = Student::whereHas('user', fn ($q) => $q->active())->count();
        $presentCount = $attendances->count();
        $attendancePercentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100) : 0;

        $formattedTime = $sessionInfo
            ? Carbon::parse($sessionInfo->start_time)->format('H:i').' - '.Carbon::parse($sessionInfo->end_time)->format('H:i')
            : '-';

        $headings = [
            'list' => SystemNotification::getMessage('Daftar Kehadiran 📝✨', 'Daftar Kehadiran'),
            'info' => SystemNotification::getMessage('Informasi Sesi 🏢✨', 'Informasi Sesi'),
        ];

        return view('pages.share-attendance', compact(
            'course',
            'attendances',
            'date',
            'availableDates',
            'assignments',
            'sessionInfo',
            'totalStudents',
            'presentCount',
            'attendancePercentage',
            'formattedTime',
            'headings'
        ));
    }
}
