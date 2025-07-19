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
        // Create accommodations table
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['room', 'apartment']);
            $table->morphs('accommodatable');
            $table->text('description');
            $table->timestamps();
        });

        // Create equipment table
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->timestamps();
        });

        // Create pivot table for accommodation-equipment relationship
        Schema::create('accommodation_equipment', function (Blueprint $table) {
            $table->foreignId('accommodation_id')
                ->constrained('accommodations')
                ->cascadeOnDelete();
            
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->cascadeOnDelete();

            $table->primary(['accommodation_id', 'equipment_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_equipment');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('accommodations');
    }
};