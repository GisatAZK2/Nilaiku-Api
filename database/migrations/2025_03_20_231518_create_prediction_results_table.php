<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('prediction_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained('academic_records');
            $table->dateTime('prediction_date');
            $table->decimal('predicted_score', 5, 2);
            $table->text('recommendation');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('prediction_results');
    }
};
