<?php

use App\Enums\NotifStyle;
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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('notif_style', NotifStyle::cases())->default(NotifStyle::Cheerful->value);
            $table->string('primary_color')->nullable();
            $table->string('font')->default('Inter');
            $table->string('content_width')->default('full');
            $table->string('border_radius')->default('lg');
            $table->boolean('top_navigation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
