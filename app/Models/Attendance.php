<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
            'date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function courseSchedule()
    {
        return $this->belongsTo(CourseSchedule::class);
    }
}
