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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('major_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->enum('work', ['employed', 'unemployed', 'seeking', 'continuing_study'])->default('continuing_study');
            $table->text('work_address')->nullable();
            $table->text('last_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
