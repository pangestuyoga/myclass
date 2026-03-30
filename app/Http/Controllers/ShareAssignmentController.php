<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShareAssignmentController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $course = Course::where('sharing_token', $token)->firstOrFail();

        // Retrieve available assignments for the dropdown (from the whole course)
        $availableAssignments = Assignment::where('course_id', $course->id)
            ->with('classSession')
            ->orderBy('created_at', 'desc')
            ->get();

        // Default: If session_id is provided, try to find an assignment for that session, or fallback to latest assignment.
        $assignmentId = $request->query('assignment_id');
        $sessionId = $request->query('session_id');

        $assignment = null;
        if ($assignmentId) {
            $assignment = $availableAssignments->firstWhere('id', $assignmentId);
        } elseif ($sessionId) {
            $assignment = $availableAssignments->firstWhere('class_session_id', $sessionId) ?? $availableAssignments->first();
        } else {
            $assignment = $availableAssignments->first();
        }

        $sessionInfo = $assignment ? $assignment->classSession : null;

        // Get submissions and calculate stats
        $submissions = collect();
        if ($assignment) {
            $submissions = AssignmentSubmission::with(['student', 'media'])
                ->where('assignment_id', $assignment->id)
                ->get();
        }

        $totalStudents = Student::whereHas('user', fn ($q) => $q->active())->count();
        $submittedCount = $submissions->count();
        $submissionPercentage = $totalStudents > 0 ? round(($submittedCount / $totalStudents) * 100) : 0;

        $formattedDeadline = $assignment && $assignment->due_date
            ? Carbon::parse($assignment->due_date)->translatedFormat('l, d F Y H:i')
            : '-';

        return view('pages.share-assignment', compact(
            'course',
            'assignment',
            'availableAssignments',
            'sessionInfo',
            'submissions',
            'totalStudents',
            'submittedCount',
            'submissionPercentage',
            'formattedDeadline'
        ));
    }
}
