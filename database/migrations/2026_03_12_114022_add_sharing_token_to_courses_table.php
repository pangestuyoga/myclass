<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('sharing_token', 64)->nullable()->unique()->after('semester');
        });

        // Populate existing courses with a random token
        \App\Models\Course::all()->each(function ($course) {
            $course->update(['sharing_token' => \Illuminate\Support\Str::random(32)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('sharing_token');
        });
    }
};
