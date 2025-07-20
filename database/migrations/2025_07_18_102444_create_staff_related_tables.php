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
        // Create staff categories table
        Schema::create('staff_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_ar')->nullable();
            $table->enum('type', ['faculty', 'administrative', 'campus', 'technical', 'support']);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_ar');
            $table->timestamps();
        });

        Schema::create('campus_units', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['management', 'maintenance', 'clinic', 'security']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
        Schema::dropIfExists('staff_categories');
    }
};