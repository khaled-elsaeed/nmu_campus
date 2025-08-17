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
            $table->foreignId('apartment_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->restrictOnDelete();
            $table->text('description')->nullable();
            $table->integer('bed_count')->nullable();
            $table->foreignId('reservation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Create equipment table
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('category_en')->nullable();
            $table->string('category_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->decimal('price_per_quantity', 10, 2)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('accommodations');
    }
};