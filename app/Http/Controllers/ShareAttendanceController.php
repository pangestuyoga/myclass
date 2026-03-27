<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Http\Request;

class ShareAttendanceController extends Controller
{
    public function show(Request $request, string $token)
    {
        app()->setLocale('id');
        $course = Course::where('sharing_token', $token)->firstOrFail();

        // Define default date: if today has no attendance, use the most recent attendance date
        $latestAttendance = Attendance::whereHas('courseSchedule', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->latest('date')
            ->first();

        $defaultDate = $latestAttendance ? $latestAttendance->date->toDateString() : now()->toDateString();
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
            ->orderBy('date', 'desc')
            ->get()
            ->pluck('date');

        $assignments = Assignment::where('course_id', $course->id)
            ->withCount('assignmentSubmissions')
            ->latest()
            ->get();

        return view('pages.share-attendance', compact('course', 'attendances', 'date', 'availableDates', 'assignments'));
    }
}
