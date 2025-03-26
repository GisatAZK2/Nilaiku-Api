<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->dateTime('input_date');
            $table->decimal('attendance', 5, 2);
            $table->decimal('hours_studied', 5, 2);
            $table->decimal('previous_scores', 5, 2);
            $table->integer('tutoring_sessions');
            $table->decimal('physical_activity', 5, 2);
            $table->decimal('sleep_hours', 5, 2);
            $table->enum('parental_involvement', ['Low', 'Medium', 'High']);
            $table->enum('access_to_resources', ['Low', 'Medium', 'High']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('academic_records');
    }
};
