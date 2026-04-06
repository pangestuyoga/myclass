<?php

namespace App\Http\Controllers;

use App\Filament\Support\SystemNotification;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $headings = [
            'hero_title' => SystemNotification::getByKey('labels.home.hero_title'),
            'hero_subtitle' => SystemNotification::getByKey('labels.home.hero_subtitle'),
            'feature_session' => SystemNotification::getByKey('labels.home.feature_session'),
            'feature_session_desc' => SystemNotification::getByKey('labels.home.feature_session_desc'),
            'feature_task' => SystemNotification::getByKey('labels.home.feature_task'),
            'feature_task_desc' => SystemNotification::getByKey('labels.home.feature_task_desc'),
            'feature_attendance' => SystemNotification::getByKey('labels.home.feature_attendance'),
            'feature_attendance_desc' => SystemNotification::getByKey('labels.home.feature_attendance_desc'),
            'feature_group' => SystemNotification::getByKey('labels.home.feature_group'),
            'feature_group_desc' => SystemNotification::getByKey('labels.home.feature_group_desc'),
        ];

        return view('welcome', compact('headings'));
    }
}
