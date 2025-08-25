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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('academic_id')->nullable()->unique();
            $table->string('national_id')->unique();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('academic_email')->nullable()->unique();
            $table->string('phone')->unique();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->enum('level', ['1', '2', '3', '4', '5']);
            $table->decimal('cum_gpa', 3, 2)->default(0.00);
            $table->decimal('score', 5, 2)->default(0.00);
            $table->foreignId('faculty_id')->constrained()->restrictOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('nationality_id')->constrained()->restrictOnDelete();
            $table->foreignId('governorate_id')->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->text('street');
            $table->boolean('is_profile_complete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
