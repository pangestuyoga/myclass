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

        $activeStudents = Student::whereHas('user', fn ($q) => $q->active())
            ->orderBy('full_name')
            ->get();

        $attendanceRecords = Attendance::whereHas('courseSchedule', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        $attendances = $activeStudents->map(fn ($student) => (object) [
            'student' => $student,
            'attended_at' => $attendanceRecords->get($student->id)?->attended_at,
            'has_attended' => $attendanceRecords->has($student->id),
        ]);

        $availableDates = ClassSession::where('course_id', $course->id)
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
        $totalStudents = $activeStudents->count();
        $presentCount = $attendanceRecords->count();
        $attendancePercentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100) : 0;

        $formattedTime = $sessionInfo
            ? Carbon::parse($sessionInfo->start_time)->translatedFormat('H:i').' - '.Carbon::parse($sessionInfo->end_time)->format('H:i')
            : '-';

        $headings = [
            'list' => SystemNotification::getByKey('labels.attendance_list.title'),
            'info' => SystemNotification::getByKey('labels.session_info.title'),
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
