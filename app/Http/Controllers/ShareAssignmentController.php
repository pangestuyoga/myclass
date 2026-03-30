<?php

namespace App\Http\Controllers;

use App\Enums\AssignmentType;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShareAssignmentController extends Controller
{
    public function show(Request $request, Course $course): View
    {

        $availableAssignments = Assignment::where('course_id', $course->id)
            ->with('classSession')
            ->orderBy('created_at', 'desc')
            ->get();

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

        $submissions = collect();
        $isGroup = false;
        $totalTargets = 0;

        if ($assignment) {
            $isGroup = $assignment->type === AssignmentType::Group;
            $submissions = AssignmentSubmission::with(['student', 'media', 'studyGroup.students'])
                ->where('assignment_id', $assignment->id)
                ->get();

            if ($isGroup) {
                $totalTargets = $assignment->studyGroups()->count();
            } else {
                $totalTargets = $assignment->students()->count();
                if ($totalTargets === 0) {
                    $totalTargets = Student::whereHas('user', fn ($q) => $q->active())->count();
                }
            }
        }

        $submittedCount = $submissions->count();
        $submissionPercentage = $totalTargets > 0 ? round(($submittedCount / $totalTargets) * 100) : 0;

        $formattedDeadline = $assignment && $assignment->due_date
            ? Carbon::parse($assignment->due_date)->translatedFormat('l, d F Y H:i')
            : '-';

        return view('pages.share-assignment', compact(
            'course',
            'assignment',
            'availableAssignments',
            'sessionInfo',
            'submissions',
            'totalTargets',
            'submittedCount',
            'submissionPercentage',
            'formattedDeadline',
            'isGroup'
        ));
    }
}
