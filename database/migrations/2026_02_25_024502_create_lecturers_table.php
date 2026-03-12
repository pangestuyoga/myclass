<?php

use App\Enums\Sex;
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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('lecturer_identification_number', 10)->unique();
            $table->string('full_name', 100);
            $table->string('phone_number', 20);
            $table->enum('sex', Sex::cases());
            $table->text('address')->nullable();
            $table->date('date_of_birth');
            $table->string('place_of_birth', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
