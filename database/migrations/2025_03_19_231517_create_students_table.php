<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
            
            ->unique()->constrained('users');
            $table->string('name', 100);
            $table->unsignedInteger('age');
            $table->enum('gender', ['male', 'female']);
            $table->string('education');
            $table->boolean('is_guest')->default(false);
            $table->string('guest_session_token')->unique()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('students');
    }
};
