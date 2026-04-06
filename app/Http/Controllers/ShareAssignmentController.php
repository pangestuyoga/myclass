<?php

namespace App\Http\Controllers;

use App\Enums\AssignmentType;
use App\Filament\Support\SystemNotification;
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
            ->orderByDesc('created_at')
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
            $allSubmissions = AssignmentSubmission::with(['student', 'media', 'studyGroup.students'])
                ->where('assignment_id', $assignment->id)
                ->get();

            if ($isGroup) {
                $targets = $assignment->studyGroups()->with('students')->get();
                $submissionMap = $allSubmissions->keyBy('study_group_id');

                $submissions = $targets->map(fn ($group) => (object) [
                    'studyGroup' => $group,
                    'student' => $submissionMap->get($group->id)?->student,
                    'submitted_at' => $submissionMap->get($group->id)?->submitted_at,
                    'has_submitted' => $submissionMap->has($group->id),
                    'submission' => $submissionMap->get($group->id),
                ]);
                $totalTargets = $targets->count();
            } else {
                $targets = $assignment->students()->orderBy('full_name')->get();
                if ($targets->isEmpty()) {
                    $targets = Student::whereHas('user', fn ($q) => $q->active())->orderBy('full_name')->get();
                }
                $submissionMap = $allSubmissions->keyBy('student_id');

                $submissions = $targets->map(fn ($student) => (object) [
                    'student' => $student,
                    'submitted_at' => $submissionMap->get($student->id)?->submitted_at,
                    'has_submitted' => $submissionMap->has($student->id),
                    'submission' => $submissionMap->get($student->id),
                ]);
                $totalTargets = $targets->count();
            }
        }

        $submittedCount = $allSubmissions ? $allSubmissions->unique($isGroup ? 'study_group_id' : 'student_id')->count() : 0;
        $submissionPercentage = $totalTargets > 0 ? round(($submittedCount / $totalTargets) * 100) : 0;

        $formattedDeadline = $assignment && $assignment->due_date
            ? Carbon::parse($assignment->due_date)->translatedFormat('l, d F Y H:i')
            : '-';

        $headings = [
            'list' => SystemNotification::getByKey('labels.submission_list.title'),
            'info' => SystemNotification::getByKey('labels.assignment_info_heading.title'),
        ];

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
            'isGroup',
            'headings'
        ));
    }
}
