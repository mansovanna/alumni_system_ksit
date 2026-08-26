<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('event_type', ['job_fair', 'reunion', 'workshop', 'seminar', 'other'])->nullable();
            $table->string('banner_image')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('registration_deadline')->nullable();
            $table->integer('max_participants');
            $table->text('external_link')->nullable();
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
